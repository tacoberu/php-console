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
	 * @param string $defaultcommand Make default signature with first argument
	 * as command name. If unnused, set this value.
	 */
	function __construct($defaultcommand = Null)
	{
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
		$program = array_shift($args);

		$req = new Request($program, $pwd);
		$req->addRawData($args);
		$req->applyRules($this->getDefaultSignature());

		return $req;
	}



	private function getDefaultSignature()
	{
		$sig = new OptionSignature();
		if ($this->defaultcommand) {
			$sig->addArgumentDefault('command', $sig::TYPE_TEXT, $this->defaultcommand, 'command');
		}
		else {
			$sig->addArgument('command', $sig::TYPE_TEXT, 'command');
		}
		return $sig;
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
