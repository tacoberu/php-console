<?php
/**
 * Copyright (c) since 2004 Martin Takáč
 * @author Martin Takáč <martin@takac.name>
 */

namespace Taco\Console;


/**
 * Reprezentuje popis závislost a implementaci vstupních bodů. Jeden kommand
 * je jedna akce, ale může být spouštěna ve více módech (dry-run, check a podobně)
 * @author Martin Takáč <martin@takac.name>
 */
interface Command
{

	/**
	 * Provede výkonný kód.
	 */
	function execute(Options $opts);

}



/**
 * Command běží v transakci. Když se nepovede je tu uklízecí metoda.
 * @author Martin Takáč <martin@takac.name>
 */
interface TransactiableCommand extends Command
{

	/**
	 * Provede commit při úspěchu.
	 */
	function commit();



	/**
	 * Provede rollback při neúspěchu.
	 */
	function rollback();

}



/**
 * Provide services.
 * @author Martin Takáč <martin@takac.name>
 */
interface Container
{

}



/**
 * @author Martin Takáč <martin@takac.name>
 */
interface Resolver
{
	function resolve($name);
}



/**
 * Popisuje objekty. Doplní k nim informace.
 * @author Martin Takáč <martin@takac.name>
 */
interface Describe
{

	/**
	 * @return string
	 */
	function getType();



	/**
	 * @return stdClass
	 */
	function getMetaInfo();



	/**
	 * @return array of string
	 */
	function getDepends();

}



/**
 * Zpracování vstupních parametrů.
 * @author Martin Takáč <martin@takac.name>
 */
interface Output
{

	/**
	 * @param string|Formatable $content
	 */
	function notice($content);



	/**
	 * @param string|Formatable $content
	 */
	function error($content);

}



/**
 * Formátovaná data do výstupu.
 * @author Martin Takáč <martin@takac.name>
 */
interface Data
{

}
