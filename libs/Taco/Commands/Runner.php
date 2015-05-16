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
			$request = RequestParser::fromEnv($GLOBALS);
			$command = $this->dispatchCommand($request);
			$options = Options::fromArray($request->args, self::assertType('Taco\Commands\OptionSignature', $command->getOptionSignature()));
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



	/**
	 * Výběr akcí.
	 * @return Command
	 */
	private function dispatchCommand($request)
	{
		if (empty($request->command)) {
			throw new RuntimeException("Not used command name. Try the command `help' to list all options.", 1);
		}

		return $this->container->getCommand($request->command);
	}



	private static function assertType($type, $value)
	{
		if ($value instanceof $type) {
			return $value;
		}
		throw new RuntimeException("invalid return type");
	}

}
