<?php
/**
 * Copyright (c) since 2004 Martin Takáč
 * @author Martin Takáč <martin@takac.name>
 */

namespace Taco\Console;


use InvalidArgumentException;


/**
 * Seznam zpracovaných vstupních hodnot. To je to, co dostane command.
 */
class Options
{

	/**
	 * Volba a hodnota předaná z CLI.
	 */
	private $options = array();



	function __construct(array $args)
	{
		$this->options = $args;
	}


	/**
	 * @param string $name Jméno parametru.
	 * @return mixin
	 */
	function getOption($name)
	{
		if (array_key_exists($name, $this->options)) {
			return $this->options[$name];
		}

		throw new InvalidArgumentException("Option `$name' not found.");
	}


}
