<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


use UnexpectedValueException,
	Exception;
use Nette\Utils\Validators;


class TypeException extends UnexpectedValueException
{

	private $typename, $value;

	function __construct($typename, $value, $message = Null, $code = 0, Exception $previous = NULL)
	{
		if (empty($message)) {
			$message = "Unrecognizable type of {$typename}: `{$value}'.";
		}
		parent::__construct($message, $code, $previous);
		$this->typename = $typename;
		$this->value = $value;
	}


	function getTypeName()
	{
		return $this->typename;
	}


	function getValue()
	{
		return $this->value;
	}

}



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
		throw new TypeException('int', $val);
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
		throw new TypeException('float', $val);
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
				throw new TypeException('bool', $val);
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
			$typename = "enum(" . implode(',', $this->options) . ")";
			throw new TypeException($typename, $val, "Unrecognizable type of {$typename}: empty.");
		}
		if (! in_array($val, $this->options)) {
			throw new TypeException("enum(" . implode(',', $this->options) . ")", $val);
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
			$typename = "set(" . implode(',', $this->options) . ")";
			throw new TypeException($typename, $val, "Unrecognizable type of {$typename}: empty.");
		}

		$val = explode($this->sep, $val);
		$missing = array_diff($val, $this->options);
		if (count($missing)) {
			throw new TypeException("set(" . implode(',', $this->options) . ")", implode(',', $missing));
		}
		return $val;
	}


}
