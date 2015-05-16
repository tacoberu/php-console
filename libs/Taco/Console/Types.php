<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;



use InvalidArgumentException;


interface Type
{

	/**
	 * Přetypuje vstupní hodnotu na hodnotu odpovídající typu.
	 * @param string Všechno na vstupu je string.
	 */
	function cast($val);

}


/**
 * Jedna možnost z vícero.
 */
class TypeEnum implements Type
{

	private $options = array();


	/**
	 * @param array Pole možností.
	 */
	function __construct(array $xs)
	{
		$this->options = $xs;
	}



	/**
	 * @param string 'male'
	 */
	function cast($val)
	{
		if (! in_array($val, $this->options)) {
			throw new InvalidArgumentException("`$val' is not of enum(" . implode(',', $this->options) . ").");
		}
		return $val;
	}


}



/**
 * Více možností z vícero.
 */
class TypeSet implements Type
{

	private $options = array();

	private $set;


	/**
	 * @param array pole voleb
	 * @param string čím se budou oddělovat hodnoty na vstupu.
	 */
	function __construct(array $xs, $sep = ',')
	{
		$this->options = $xs;
		$this->sep = $sep;
	}



	/**
	 * @param string 'male,femal,sheep'
	 * @return array
	 */
	function cast($val)
	{
		$val = explode($this->sep, $val);
		$missing = array_diff($val, $this->options);
		if (count($missing)) {
			throw new InvalidArgumentException("`" . implode(',', $missing). "' is not of set(" . implode(',', $this->options) . ").");
		}
		return $val;
	}


}
