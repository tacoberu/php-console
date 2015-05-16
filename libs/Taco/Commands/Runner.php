<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Commands;


use Exception,
	RuntimeException;


class Runner
{

	private $container;


	/**
	 * @param Container $container
	 */
	function __construct($container)
	{
		$this->container = $container;
	}



	function run()
	{
		try {
			$output = $this->container->getOutput();
			$request = $this->parseFromEnv($GLOBALS);
			$command = $this->dispatchCommand($request);
			$options = Options::fromArray($request->getArguments(), self::assertType('Taco\Commands\OptionSignature', $command->getOptionSignature()));
			return $command->execute($options);
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



	private function parseFromEnv(array $xs)
	{
		$parser = $this->container->getParser();
		$request = $parser->parse($xs);
		return $request;
	}



	/**
	 * Výběr akcí.
	 * @return Command
	 */
	private function dispatchCommand($request)
	{
		if (! $request->hasCommand()) {
			throw new RuntimeException("Not used command name. Try the command `help' to list all options.", 1);
		}

		return $this->container->getCommand($request->getCommand());
	}



	private static function assertType($type, $value)
	{
		if ($value instanceof $type) {
			return $value;
		}
		throw new RuntimeException("invalid return type");
	}

}
