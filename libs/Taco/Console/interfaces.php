<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


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
	 * Name of application.
	 * @return string
	 */
	function getApplicationName();


	/**
	 * Description of application.
	 * @return string
	 */
	function getApplicationDescription();


	/**
	 * @return string
	 */
	function getAuthor();


	/**
	 * @return string
	 */
	function getAuthorEmail();


	/**
	 * @param string $name Name of command.
	 * @return Command with all dependencies.
	 */
	function getCommand($name);


	/**
	 * Seznam všechn commandů, které jsou k dispozici.
	 * @return array of Command
	 */
	function getCommandList();


	/**
	 * Zapisování na výstup.
	 * @return Output
	 */
	function getOutput();


	/**
	 * Parser of input arguments.
	 * @return RequestParser
	 */
	function getRequestParser();


	/**
	 * Generic command as help, version, etc.
	 * @return OptionSignature
	 */
	function getGenericSignature();


	/**
	 * Verze aplikace.
	 * @return string like 0.0.1
	 */
	function getVersion();


	/**
	 * Prepare before execute.
	 */
	function beforeExecute(Options $options);


	/**
	 * Clean after execute.
	 */
	function afterExecute(Options $options);


}



/**
 * Zpracování vstupních parametrů.
 */
interface RequestParser
{

	/**
	 * @param array
	 * @return Request
	 */
	function parse(array $env);

}
