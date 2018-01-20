<?php
/**
 * @copyright 2016 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Console;


use Exception,
	LogicException;


/**
 * @author Martin Takáč <martin@takac.name>
 */
class Runner
{

	/**
	 * @var Container
	 */
	private $container;


	/**
	 * @param Container $container
	 */
	function __construct(Container $container)
	{
		$this->container = $container;
	}



	/**
	 * Hodnoty prostředí. Takže typicky z GLOBALS, nebo cokoliv, co umí
	 * zpracovat nakonfigurovanej parser, viz: Container::getParser().
	 * @param array
	 */
	function run(array $env)
	{
		try {
			$outputs = self::assertEmpty($this->container->findByType(Output::class), Output::class);

			// Jako nouzovka nám stačí libovolný výstup.
			$output = reset($outputs);

			$parser = reset(self::assertEmpty($this->container->findByType(RequestParser::class), RequestParser::class));
			$request = $parser->parse($env);
			$request->applyRules($this->buildCommandOptionSignature('help'));

			$this->container->addInstance($request);

			// Vybrat správný výstup podle argumentů.
			if (count($outputs) > 1) {
				$request->applyRules($this->buildOutputOptionSignature($outputs));
				$args = $request->getOptions()->asArray();
				foreach ($outputs as $x) {
					if (Utils::parseClassName(get_class($x), 'Output') == $args['output']) {
						$output = $x;
						break;
					}
				}
			}

			// @TODO Samozřejmě nemůže být jen první. Co když je jich více? Co když jsou stromově zanořené?
			foreach (self::assertEmpty($this->container->findByType(Command::class), Command::class) as $def) {
				if ($def->getMetaInfo()->name === $request->getOption('command')) {
					$command = $def;
					break;
				}
			}
			if ( ! isset($command)) {
				throw new LogicException("Command not found.");
			}
			$request->applyRules($command->getOptionSignature());

			$args = $request->getOptions()->asArray();
			unset($args['command']);
			unset($args['output']);

			$deps = [];
			foreach ($command->getDepends() as $type) {
				switch($type) {
					case Output::class:
						$deps[] = $output;
						break;
					default:
						$deps[] = reset(self::assertEmpty($this->container->findByType($type), $type));
				}
			}

			switch (True) {
				case $command instanceof DescribedCommand:
					$invoker = $command->getInvoker();
					if (is_callable($invoker)) {
						return (int) call_user_func_array($invoker, array_merge($deps, $args));
					}
					elseif (is_subclass_of($invoker, Command::class)) {
						$invoker = $this->buildInvoker($invoker, $deps);
						return (int) $invoker->execute($request->getOptions());
					}
					else {
						throw new LogicException("Unsupported type of invoker: `" . $invoker . "'.");
					}
				default:
					throw new LogicException("Unsupported type of command: `" . get_class($command) . "'.");
			}
		}
		catch (Exception $e) {
			if (isset($output)) {
				$output->error($e->getMessage());
			}
			else {
				echo $e->getMessage() . PHP_EOL;
			}
			return ($e->getCode() ? $e->getCode() : 254);
		}
	}



	// -- PRIVATE ------------------------------------------------------



	private function buildCommandOptionSignature($defaultcommand = null)
	{
		$sig = new OptionSignature();
		if ($defaultcommand) {
			$sig->addArgumentDefault('command', $sig::TYPE_TEXT, $defaultcommand, 'command');
		}
		else {
			$sig->addArgument('command', $sig::TYPE_TEXT, 'command');
		}
		return $sig;
	}



	private function buildInvoker($class, $deps)
	{
		if (empty($deps)) {
			return new $class();
		}
		elseif (count($deps) === 1) {
			return new $class($deps[0]);
		}
		elseif (count($deps) === 2) {
			return new $class($deps[0], $deps[1]);
		}
		elseif (count($deps) === 3) {
			return new $class($deps[0], $deps[1], $deps[2]);
		}
		elseif (count($deps) === 4) {
			return new $class($deps[0], $deps[1], $deps[2], $deps[3]);
		}
		elseif (count($deps) === 5) {
			return new $class($deps[0], $deps[1], $deps[2], $deps[3], $deps[4]);
		}
		else {
			dump($class, $deps);
			throw new LogicException('Příliš mnoho argumentů konstruktoru.');
		}
	}



	private function buildOutputOptionSignature(array $outputs)
	{
		$sgn = new OptionSignature();
		$xs = [];
		foreach ($outputs as $x) {
			$xs[] = Utils::parseClassName(get_class($x), 'Output');
		}
		$sgn->addOption('output', new TypeEnum($xs), reset($xs), 'Formát výstupu');
		return $sgn;
	}



	/**
	 * @param array
	 * @param string
	 * @return array
	 */
	private static function assertEmpty($xs, $name)
	{
		if (empty($xs)) {
			throw new LogicException(sprintf('Not resolve %s.', $name));
		}
		return $xs;
	}

}
