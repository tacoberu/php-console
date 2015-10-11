<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


use Exception,
	LogicException;


class Runner
{

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
			$request = $this->container->getRequestParser()->parse($env);
			$this->container->setRequest($request);
			$command = $this->container->getFrontCommand();
			$request->applyRules($command->getOptionSignature());
			switch (True) {
				case $command instanceof TransactiableCommand:
					try {
						$state = $command->execute($request->getOptions());
					}
					catch (Exception $e) {
						$command->rollback();
						throw $e;
					}
					$command->commit();
					return (int)$state;
				case $command instanceof Command:
					return (int)$command->execute($request->getOptions());
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



	private static function ___assertType($type, $value)
	{
		if ($value instanceof $type) {
			return $value;
		}
		throw new LogicException("Invalid type (`$type') of value.");
	}

}
