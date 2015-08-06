<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


use InvalidArgumentException;


class RequestEnvParser implements RequestParser
{

	private $defaultcommand;

	/**
	 * Závislosti na služby. I výstup je služba-
	 */
	function __construct($defaultcommand = Null)
	{
		$this->defaultcommand = $defaultcommand;
	}



	function parse(array $env)
	{
		$args = self::parseArgs($env);
		$pwd = self::parsePwd($env);
		$program = array_shift($args);

		$command = reset($args);
		if (empty($command) || self::isOption($command)) {
			$command = $this->defaultcommand;
		}
		else {
			$command = array_shift($args);
		}

		return new Request($pwd, $program, $command, $args);
	}



	private static function parseArgs(array $env)
	{
		if (! isset($env['argv'])) {
			throw new InvalidArgumentException('Chybí proměnná prostředí $argv.');
		}
		return $env['argv'];
	}



	private static function parsePwd(array $env)
	{
		if (empty($env['_SERVER'])) {
			throw new InvalidArgumentException('Chybí proměnná prostředí $_SERVER.');
		}
		return $env['_SERVER']['PWD'] ?: getcwd();
	}


	private static function isOption($name)
	{
		return ($name{0} == '-');
	}

}