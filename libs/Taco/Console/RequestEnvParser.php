<?php
/**
 * Copyright (c) since 2004 Martin Takáč
 * @author Martin Takáč <martin@takac.name>
 */

namespace Taco\Console;

use InvalidArgumentException;


class RequestEnvParser implements RequestParser
{

	private $defaultcommand;


	/**
	 * @param string $defaultcommand Make default signature with first argument
	 * as command name. If unnused, set this value.
	 */
	function __construct($defaultcommand = Null)
	{
		if ($defaultcommand !== Null) {
			TypeUtils::assert($defaultcommand, 'string:1..');
		}
		$this->defaultcommand = $defaultcommand;
	}



	/**
	 * @param array $env
	 * @return Request
	 */
	function parse(array $env)
	{
		$args = self::parseArgs($env);
		$pwd = self::parsePwd($env);
		if (count($args)) {
			$program = array_shift($args);
		}
		else {
			$program = 'app';
		}

		$req = new Request($program, $pwd);
		$req->addRawData($args);
		$req->applyRules($this->createDefaultSignature($pwd));

		return $req;
	}



	// -- PRIVATE ------------------------------------------------------



	private function createDefaultSignature($pwd)
	{
		$sig = new OptionSignature();
		if ($this->defaultcommand) {
			$sig->addArgumentDefault('command', $sig::TYPE_TEXT, $this->defaultcommand, 'The command name');
		}
		else {
			$sig->addArgument('command', $sig::TYPE_TEXT, 'The command name');
		}
		return $sig;
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
