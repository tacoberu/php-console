<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


/**
 * Výstup
 */
class Output
{

	function notice($content)
	{
		echo $content . PHP_EOL;
	}


	function error($content)
	{
		echo $content . PHP_EOL;
	}

}