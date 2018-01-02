<?php
/**
 * Copyright (c) since 2004 Martin Takáč
 * @author Martin Takáč <martin@takac.name>
 */

namespace Taco\Console;


/**
 * Zpracování vstupních parametrů.
 * @author Martin Takáč <martin@takac.name>
 */
interface RequestParser
{

	/**
	 * @param array
	 * @return Request
	 */
	function parse(array $env);

}
