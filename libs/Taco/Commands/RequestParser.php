<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Commands;


use InvalidArgumentException;


class RequestParser
{


	static function fromEnv(array $env)
	{
		$args = self::parseArgs($env);
		$pwd = self::parsePwd($env);
		$program = array_shift($args);
		$command = array_shift($args);
		return (object) array(
				'pwd' => $pwd,
				'program' => $program,
				'command' => $command,
				'args' => $args,
				);
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

}
