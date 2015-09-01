<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


use InvalidArgumentException;


/**
 * Seznam zpracovaných vstupních hodnot.
 */
class Options
{

	/**
	 * volba a hodnota předaná z CLI.
	 */
	private $options = array();



	function __construct(array $args)
	{
		$this->options = $args;
	}



	/**
	 * @param string $name Jméno parametru.
	 * @param mixin $default Defaultní hodnota, není-li uvedena.
	 * @return mixin.
	 */
	function getOption($name)
	{
		if (isset($this->options[$name])) {
			return $this->options[$name];
		}

		throw new InvalidArgumentException("Option `$name' not found.");
	}



	/**
	 * @return array
	 */
	function asArray()
	{
		return $this->options;
	}

}
