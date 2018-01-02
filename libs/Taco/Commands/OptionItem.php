<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Commands;


/**
 * Volba, parametr.
 */
class OptionItem
{

	/**
	 * Jméno volby.
	 */
	private $name;


	/**
	 * Důkladný popis.
	 */
	private $description;


	/**
	 * Defaultní hodnota-
	 */
	private $defaultValue;


	function __construct($name, $description)
	{
		$this->name = $name;
		$this->description = $description;
	}



	function setDefaultValue($text)
	{
		$this->defaultValue = $text;
		return $this;
	}


	function getName()
	{
		return $this->name;
	}


	function getDefaultValue()
	{
		return $this->defaultValue;
	}


	/**
	 * Zpracování vstupní hodnoty na sanitovanou. Případně to řve, kdy je blbost.
	 */
	function parse($val)
	{
		return (string)$val;
	}

}


/**
 * Nenese žádnou hodnotu, je jen příznakem. Obvykle se převede na name => True
 */
class FlagOptionItem extends OptionItem
{
}


/**
 * Omezení je definováno typem.
 */
class ConstraintOptionItem extends OptionItem
{

	private $type;


	function __construct(Type $type, $name, $description)
	{
		parent::__construct($name, $description);
		$this->type = $type;
	}


	function parse($val)
	{
		return $this->type->cast($val);
	}


}


class TextOptionItem extends OptionItem
{
}


class IntOptionItem extends OptionItem
{
	function parse($val)
	{
		return (int)$val;
	}
}


class FloatOptionItem extends OptionItem
{
	function parse($val)
	{
		return (float)$val;
	}
}


class BoolOptionItem extends OptionItem
{
	function parse($val)
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
		}
	}
}