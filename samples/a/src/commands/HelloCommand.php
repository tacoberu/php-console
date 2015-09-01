<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace App\Console;


use Taco\Console\Command,
	Taco\Console\Output,
	Taco\Console\OptionSignature,
	Taco\Console\Options;


/**
 * Reprezentuje popis závislost a implementaci vstupních bodů. Jeden kommand
 * je jedna akce, ale může být spouštěna ve více módech (dry-run, check a podobně)
 */
class HelloCommand implements Command
{

	private $output;


	/**
	 * Závislosti na služby. I výstup je služba-
	 */
	function __construct(Output $output)
	{
		$this->output = $output;
	}



	/**
	 * Jméno akce
	 * @return string
	 */
	function getName()
	{
		return 'hello';
	}



	/**
	 * Popis
	 * @return string
	 */
	function getDescription()
	{
		return 'Toto je ukázkový command.';
	}



	/**
	 * Definice pro nutné nastavení.
	 * @return OptionSignature
	 */
	function getOptionSignature()
	{
		$sign = new OptionSignature();
		$sign->addArgument('name|n', $sign::TYPE_TEXT, 'Jméno koho pozdravím.');
		$sign->addArgument('age|a', $sign::TYPE_INT, 'Věk koho pozdravím.');
		$sign->addArgumentDefault('title', $sign::TYPE_TEXT, 'sir', 'Má titul?');
		return $sign;
	}



	/**
	 * Provede výkonný kód.
	 */
	function execute(Options $opts)
	{
		var_dump(getcwd());
		$this->output->notice(sprintf("Hello %s (%d)", $opts->getOption('name'), $opts->getOption('age')));
	}


}
