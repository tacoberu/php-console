<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;



use InvalidArgumentException;
use Nette\Utils\Validators;



/**
 * Validace typu.
 */
interface Type
{

	/**
	 * Přetypuje vstupní hodnotu na hodnotu odpovídající typu.
	 * @param string Všechno na vstupu je string.
	 */
	function cast($val);

}


/**
 * Blíže neurčený text.
 * @TODO has be emtpy?
 */
class TypeText implements Type
{

	/**
	 * @param string 'male'
	 */
	function cast($val)
	{
		return (string)$val;
	}

}



/**
 * Celé číslo
 */
class TypeInt implements Type
{

	/**
	 * @param string '42'
	 */
	function cast($val)
	{
		if (Validators::is($val, 'numericint')) {
			return (int)$val;
		}
		throw new InvalidArgumentException("Unrecognizable type of int: `{$val}'.");
	}

}



/**
 * Číslo s čárkou
 */
class TypeFloat implements Type
{

	/**
	 * @param string '42.3'
	 */
	function cast($val)
	{
		if (Validators::is($val, 'numeric')) {
			return (float)$val;
		}
		throw new InvalidArgumentException("Unrecognizable type of float: `{$val}'.");
	}

}



/**
 * Boolean.
 */
class TypeBool implements Type
{

	/**
	 * @param string '42.3'
	 */
	function cast($val)
	{
		switch(strtolower($val)) {
			case 'off':
			case 'no':
			case 'n':
			case 'false':
			case '0':
				return False;
			case 'on':
			case 'true':
			case 'y':
			case 'yes':
			case '1':
				return True;
			default:
				throw new InvalidArgumentException("Unrecognizable type of bool: `{$val}'.");
		}
	}

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
		Validators::assert($xs, 'list:1..');
		$this->options = $xs;
	}



	/**
	 * @param string 'male'
	 */
	function cast($val)
	{
		if (empty($val)) {
			throw new InvalidArgumentException("Value must by of enum(" . implode(',', $this->options) . ").");
		}
		if (! in_array($val, $this->options)) {
			throw new InvalidArgumentException("Value `$val' is not of enum(" . implode(',', $this->options) . ").");
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

	private $sep;


	/**
	 * @param array pole voleb
	 * @param string čím se budou oddělovat hodnoty na vstupu.
	 */
	function __construct(array $xs, $sep = ',')
	{
		Validators::assert($xs, 'list:1..');
		Validators::assert($sep, 'string:1..');
		$this->options = $xs;
		$this->sep = $sep;
	}



	/**
	 * @param string 'male,femal,sheep'
	 * @return array
	 */
	function cast($val)
	{
		if (empty($val)) {
			throw new InvalidArgumentException("Value must by of set(" . implode(',', $this->options) . ").");
		}

		$val = explode($this->sep, $val);
		$missing = array_diff($val, $this->options);
		if (count($missing)) {
			throw new InvalidArgumentException("Value `" . implode(',', $missing). "' is not of set(" . implode(',', $this->options) . ").");
		}
		return $val;
	}


}
