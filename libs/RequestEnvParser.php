<?php
/**
 * Copyright (c) since 2004 Martin Takáč
 * @author Martin Takáč <martin@takac.name>
 */

namespace Taco\Console;

use InvalidArgumentException;


class RequestEnvParser implements RequestParser
{


	/**
	 * @param array $env
	 * @return Request
	 */
	function parse(array $env)
	{
		$args = self::parseArgs($env);
		$pwd = self::parsePwd($env);
		$program = array_shift($args);

		$req = new Request($program, $pwd);
		$req->addRawData($args);
		$req->applyRules($this->getDefaultSignature());

		return $req;
	}



	// -- PRIVATE ------------------------------------------------------



	private function getDefaultSignature()
	{
		return new OptionSignature();
	}



	private static function parseArgs(array $env)
	{
		if ( ! isset($env['argv'])) {
			throw new InvalidArgumentException("Missing `\$argv' environment variable.");
		}
		return $env['argv'];
	}



	private static function parsePwd(array $env)
	{
		if (empty($env['_SERVER'])) {
			throw new InvalidArgumentException("Missing `\$_SERVER' environment variable.");
		}
		return $env['_SERVER']['PWD'] ?: getcwd();
	}


}
