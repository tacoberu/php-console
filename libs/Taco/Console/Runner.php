<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


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



	/**
	 * Hodnoty prostředí. Takže typicky z GLOBALS, nebo cokoliv, co umí
	 * zpracovat nakonfigurovanej parser, viz: Container::getParser().
	 * @param array
	 */
	function run(array $env)
	{
		try {
			$output = $this->container->getOutput();
			$request = $this->parseFromEnv($env);
			$command = $this->dispatchCommand($request);
			$signature = self::assertType('Taco\Console\OptionSignature', $command->getOptionSignature());
			$signature = $this->mergeWithGenericSignature($signature);
			$options = Options::fromArray($request->getArguments(), $signature);
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
	 * @return Request
	 */
	private function parseFromEnv(array $env)
	{
		$parser = $this->container->getParser();
		$request = $parser->parse($env);
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



	private function mergeWithGenericSignature(OptionSignature $with)
	{
		$base = $this->container->getGenericSignature();
		return $base->merge($with);
	}



	private static function assertType($type, $value)
	{
		if ($value instanceof $type) {
			return $value;
		}
		throw new RuntimeException("invalid return type");
	}

}
