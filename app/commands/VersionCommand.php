<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Commands;


use Nette;


/**
 * Reprezentuje popis závislost a implementaci vstupních bodů. Jeden kommand
 * je jedna akce, ale může být spouštěna ve více módech (dry-run, check a podobně)
 */
class VersionCommand implements Command
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
		return 'version';
	}



	/**
	 * Popis
	 */
	function getDescription()
	{
		return 'Verze aplikace.';
	}



	/**
	 * Definice pro nutné nastavení.
	 * @return OptionSignature
	 */
	function getOptionSignature()
	{
		return new OptionSignature();
	}



	/**
	 * Provede výkonný kód.
	 */
	function execute(Options $opts)
	{
		$this->output->notice("0.0.1");
	}


}
