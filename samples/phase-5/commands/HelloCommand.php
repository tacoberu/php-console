<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace App\Console;


use Taco\Console\Command,
	Taco\Console\Output,
	Taco\Console\Options;


/**
 * @name hello
 * @argument("text", "name|n", "Your name")
 * @argument("int", "age", "Your age")
 * @optional("text", "title", "Title", "sir")
 * Implementation of example command.
 */
class HelloCommand implements Command
{

	private $output;


	/**
	 * @param Output $output Where show documentation.
	 */
	function __construct(Output $output)
	{
		$this->output = $output;
	}



	/**
	 * Provede výkonný kód.
	 */
	function execute(Options $opts)
	{
		$this->output->notice(sprintf("Hello %s %s (%d)", $opts['title'], $opts['name'], $opts['age']));
	}


}
