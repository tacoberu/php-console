<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


/**
 * ...
 */
class Request
{

	private $pwd;
	private $program;
	private $command;
	private $args;


	/**
	 *	...
	 */
	function __construct($pwd, $program, $command, array $args)
	{
		$this->pwd = $pwd;
		$this->program = $program;
		$this->command = $command;
		$this->args = $args;
	}



	/**
	 * @return bool
	 */
	function hasCommand()
	{
		return !empty($this->command);
	}



	/**
	 * Název požadované akce.
	 * @return string
	 */
	function getCommand()
	{
		return $this->command;
	}



	/**
	 * Aktuální umístění, odkud se volá. Vztažný bod filsesystemu.
	 * @return string
	 */
	function getWorkingDirectory()
	{
		return $this->pwd;
	}



	/**
	 * @return array
	 */
	function getArguments()
	{
		return $this->args;
	}
}
