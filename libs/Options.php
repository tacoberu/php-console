<?php
/**
 * Copyright (c) since 2004 Martin Takáč
 * @author Martin Takáč <martin@takac.name>
 */

namespace Taco\Console;

use ArrayAccess;
use InvalidArgumentException,
	LogicException;


/**
 * Seznam zpracovaných vstupních hodnot. To je to, co dostane command.
 */
class Options implements ArrayAccess
{

	/**
	 * Volba a hodnota předaná z CLI.
	 */
	private $items = array();



	function __construct(array $args)
	{
		$this->items = $args;
	}



	/**
	 * @param string $name Jméno parametru.
	 * @return mixin
	 */
	function getOption($name)
	{
		if (array_key_exists($name, $this->items)) {
			return $this->items[$name];
		}

		throw new InvalidArgumentException("Option `$name' not found.");
	}



	/**
	 * @return array
	 */
	function asArray()
	{
		return $this->items;
	}



	/**
	 * Returns a item.
	 * @return mixed
	 */
	function offsetGet($name)
	{
		return $this->getOption($name);
	}



	/**
	 * Determines whether a item exists.
	 * @return bool
	 */
	function offsetExists($name)
	{
		return array_key_exists($name, $this->items);
	}



	function offsetSet($key, $value)
	{
		throw new LogicException("Method `set` is not supported.");
	}



	function offsetUnset($key)
	{
		throw new LogicException("Method `unset` is not supported.");
	}

}
