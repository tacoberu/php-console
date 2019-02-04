<?php
/**
 * Copyright (c) since 2004 Martin Takáč
 * @author Martin Takáč <martin@takac.name>
 */

namespace Taco\Console;

use InvalidArgumentException;


class RequestEnvParser implements RequestParser
{

	private $signature;


	static function createCommanded($default = Null)
	{
		$sig = new OptionSignature();
		if ($default) {
			$sig->addArgumentDefault('command', $sig::TYPE_TEXT, $default, 'The command name');
		}
		else {
			$sig->addArgument('command', $sig::TYPE_TEXT, 'The command name');
		}
		$sig->addFlag('trace', 'Display the error trace of application.');
		//~ $sig->addArgumentDefault('working-dir', $sig::TYPE_TEXT, $pwd, 'If specified, use the given directory as working directory.');

		return new static($sig);
	}



	function __construct(OptionSignature $signature = Null)
	{
		$this->signature = $signature;
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
		if ($this->signature) {
			$req->applyRules($this->signature);
		}

		return $req;
	}



	// -- PRIVATE ------------------------------------------------------



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
