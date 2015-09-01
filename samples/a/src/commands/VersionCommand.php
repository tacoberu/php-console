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
class VersionCommand implements Command
{

	private $output;

	private $version;


	/**
	 * Závislosti na služby. I výstup je služba.
	 * @param Output $output
	 * @param int $version
	 */
	function __construct(Output $output, $version)
	{
		$this->output = $output;
		$this->version = $version;
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
		$this->output->notice('v' . $this->version);
	}


}
