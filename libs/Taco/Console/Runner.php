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

			$command = $this->resolveCommand($request);
			$request->applyRules($command->getOptionSignature());

			$args = $request->getOptions()->asArray();
			unset($args['command']);
			unset($args['output']);

			$orig = getcwd();
			if (isset($args['working-dir'])) {
				chdir($args['working-dir']);
				unset($args['working-dir']);
			}

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
						$invoker = Utils::newInstance($invoker, $deps);
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
				$output->error('<bg=red>' . $e->getMessage() . '</>');
			}
			else {
				echo $e->getMessage() . PHP_EOL;
			}

			if ($request->getOption('trace')) {
				throw $e;
			}

			return ($e->getCode() ? $e->getCode() : 254);
		}
	}



	// -- PRIVATE ------------------------------------------------------



	private function resolveCommand(Request $request)
	{
		// @TODO Samozřejmě nemůže být jen první. Co když je jich více? Co když jsou stromově zanořené?
		foreach (self::assertEmpty($this->container->findByType(Command::class), Command::class) as $def) {
			if ($def->getMetaInfo()->name === $request->getOption('command')) {
				return $def;
			}
		}
		throw new LogicException("Command `" . $request->getOption('command') . "' not found.");
	}



	private function buildOutputOptionSignature(array $outputs)
	{
		$sgn = new OptionSignature();
		$xs = [];
		foreach ($outputs as $x) {
			$xs[] = Utils::parseClassName(get_class($x), 'Output');
		}
		$sgn->addOption('output', new TypeEnum($xs), reset($xs), 'The output format');
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
