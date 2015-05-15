<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Commands;


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
	 */
	function getName()
	{
		return 'hello';
	}



	/**
	 * Popis
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
		$sign->addArgument('name', $sign::TYPE_TEXT, 'Jméno koho pozdravím.');
		$sign->addArgument('age', $sign::TYPE_INT, 'Věk koho pozdravím.');
		$sign->addOption('title', 'sir', $sign::TYPE_TEXT, 'Má titul?');
		return $sign;
	}



	/**
	 * Provede výkonný kód.
	 */
	function execute(Options $opts)
	{
		$this->output->notice(sprintf("Hello %s", $opts->getOption('name')));
	}


}
