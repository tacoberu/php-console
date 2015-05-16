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
interface Command
{


	/**
	 * Jméno akce, commandu
	 * @return string
	 */
	function getName();


	/**
	 * Popis
	 * @return string
	 */
	function getDescription();


	/**
	 * Definice pro nutné nastavení.
	 * @return OptionSignature
	 */
	function getOptionSignature();


	/**
	 * Provede výkonný kód.
	 */
	function execute(Options $opts);


}


/**
 * Command běží v transakci. Když se nepovede je tu uklízecí metoda.
 */
interface TransactiableCommand extends Command
{

	/**
	 * Provede inicializaci a začne transakci.
	 */
	function prepare();


	/**
	 * Provede rollback při neuspěchu.
	 */
	function rollback();
}



/**
 * Provide services.
 */
interface Container
{

	/**
	 * @param string $name Name of command.
	 * @return Command with all dependencies.
	 */
	function getCommand($name);


	/**
	 * @return Output
	 */
	function getOutput();


	/**
	 * @return Parser
	 */
	function getParser();

}



interface RequestParser
{

	/**
	 * @return ?
	 */
	function parse(array $env);

}
