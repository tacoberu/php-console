<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Commands;


/**
 * Výstup
 */
class Output
{

	function notice($content)
	{
		echo $content . PHP_EOL;
	}

}
