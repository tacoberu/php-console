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
			$output = $this->container->getOutput();
			$request = $this->parseFromEnv($env);
			$request->applyRules($this->getGenericSignature());
			$command = $this->dispatchCommand($request);
			$request->applyRules($command->getOptionSignature());
			return $command->execute($request->getOptions());
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
		$parser = $this->container->getRequestParser();
		$request = $parser->parse($env);
		return $request;
	}



	/**
	 * Výběr akcí.
	 * @return Command
	 */
	private function dispatchCommand($request)
	{
		if (! $request->getOption('command')) {
			throw new RuntimeException("Not used command name. Try the command `help' to list all options.", 1);
		}

		return $this->container->getCommand($request->getOption('command'));
	}



	private function getGenericSignature()
	{
		return $this->container->getGenericSignature();
	}



	private static function assertType($type, $value)
	{
		if ($value instanceof $type) {
			return $value;
		}
		throw new RuntimeException("invalid return type");
	}

}
