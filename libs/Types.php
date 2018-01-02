<?php
/**
 * Copyright (c) since 2004 Martin Takáč
 * @author Martin Takáč <martin@takac.name>
 */

namespace Taco\Console;

use UnexpectedValueException,
	Exception;


/**
 * @author Martin Takáč <martin@takac.name>
 */
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
 * @author Martin Takáč <martin@takac.name>
 */
interface Type
{

	/**
	 * Přetypuje vstupní hodnotu na hodnotu odpovídající typu.
	 * @param string Všechno na vstupu je string.
	 */
	function cast($val);


	function __toString();

}



/**
 * Blíže neurčený text.
 * @TODO has be empty?
 * @author Martin Takáč <martin@takac.name>
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



	function __toString()
	{
		return 'text';
	}

}



/**
 * Celé číslo
 * @author Martin Takáč <martin@takac.name>
 */
class TypeInt implements Type
{

	/**
	 * @param string '42'
	 */
	function cast($val)
	{
		if (TypeUtils::is($val, 'numericint')) {
			return (int)$val;
		}
		throw new TypeException('int', $val);
	}



	function __toString()
	{
		return 'int';
	}

}



/**
 * Číslo s čárkou
 * @author Martin Takáč <martin@takac.name>
 */
class TypeFloat implements Type
{

	/**
	 * @param string '42.3'
	 */
	function cast($val)
	{
		if (TypeUtils::is($val, 'numeric')) {
			return (float)$val;
		}
		throw new TypeException('float', $val);
	}


	function __toString()
	{
		return 'float';
	}


}



/**
 * Boolean.
 * @author Martin Takáč <martin@takac.name>
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



	function __toString()
	{
		return 'bool';
	}

}



/**
 * Jedna možnost z vícero.
 * @author Martin Takáč <martin@takac.name>
 */
class TypeEnum implements Type
{

	private $options = array();


	/**
	 * @param array Pole možností.
	 */
	function __construct(array $xs)
	{
		TypeUtils::assert($xs, 'list:1..');
		foreach ($xs as $x) {
			TypeUtils::assert($x, 'string:1..');
		}
		$this->options = $xs;
	}



	/**
	 * @param string 'male'
	 */
	function cast($val)
	{
		if (empty($val)) {
			throw new TypeException((string)$this, $val, "Unrecognizable type of {$this}: empty.");
		}
		if (! in_array($val, $this->options)) {
			throw new TypeException((string)$this, $val);
		}
		return $val;
	}



	function __toString()
	{
		return implode('|', $this->options);
	}



	/**
	 * @return [string]
	 */
	function getOptions()
	{
		return $this->options;
	}


}



/**
 * Více možností z vícero.
 * @author Martin Takáč <martin@takac.name>
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
		TypeUtils::assert($xs, 'list:1..');
		TypeUtils::assert($sep, 'string:1..');
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
			throw new TypeException((string)$this, $val, "Unrecognizable type of {$this}: empty.");
		}

		$val = explode($this->sep, $val);
		$missing = array_diff($val, $this->options);
		if (count($missing)) {
			throw new TypeException((string)$this, implode(',', $missing));
		}
		return $val;
	}



	function __toString()
	{
		return 'set(' . implode(',', $this->options) . ')';
	}



	/**
	 * @return [string]
	 */
	function getOptions()
	{
		return $this->options;
	}

}
