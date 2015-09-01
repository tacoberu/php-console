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
			//~ $output = $this->container->getOutput();
			$request = $this->parseFromEnv($env);
			$this->container->setRequest($request);
			$request->applyRules($this->getGenericSignature());
			$command = $this->dispatchCommand($request);
			$request->applyRules($command->getOptionSignature());
			$options = $request->getOptions();

			$this->beforeExecute($options);
			$flag = True;
			$state = $command->execute($options);
			$this->afterExecute($options);
			return (int)$state;
		}
		catch (Exception $e) {
			if (isset($flag)) {
				$this->afterExecute($options);
			}
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
		return $parser->parse($env);
	}



	/**
	 * Výběr akce.
	 * @param Request $request
	 * @return Command
	 */
	private function dispatchCommand(Request $request)
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



	private function beforeExecute(Options $options)
	{
		return $this->container->beforeExecute($options);
	}



	private function afterExecute(Options $options)
	{
		return $this->container->afterExecute($options);
	}



	private static function assertType($type, $value)
	{
		if ($value instanceof $type) {
			return $value;
		}
		throw new RuntimeException("invalid return type");
	}

}
