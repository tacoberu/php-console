<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


/**
 * Defaultní implementace zobrazení verze aplikace.
 */
class VersionCommand implements Command
{

	private $output;

	private $version;


	/**
	 * Závislosti na služby. I výstup je služba-
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
		$this->output->notice($this->version);
	}


}
