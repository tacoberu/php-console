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
 * Vypsání všech souborů z cesty. Demonstrujeme tím funkčnost work-directori.
 */
class LsCommand implements Command
{

	private $output;

	/**
	 * Závislosti na služby. I výstup je služba.
	 * @param Output $output
	 * @param int $version
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
		return 'ls';
	}



	/**
	 * Popis
	 */
	function getDescription()
	{
		return 'Vypsání všech souborů z cesty. Demonstrujeme tím funkčnost work-directori.';
	}



	/**
	 * Definice pro nutné nastavení.
	 * @return OptionSignature
	 */
	function getOptionSignature()
	{
		return new OptionSignature();
	}



	function execute(Options $opts)
	{
		$this->output->notice('mmnt');
		return 0;
	}


}
