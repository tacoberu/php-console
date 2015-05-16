<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


use Nette\Utils\Validators;


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
	 * Defaultní hodnota.
	 */
	private $defaultValue;


	/**
	 * Obsahuje defaultní hodnotu? Může být i NULL, nebo False.
	 */
	private $useDefaultValue = False;


	function __construct($name, $description)
	{
		$this->name = $name;
		$this->description = $description;
	}



	function setDefaultValue($text)
	{
		$this->defaultValue = $text;
		$this->useDefaultValue = True;
		return $this;
	}


	function getName()
	{
		return $this->name;
	}


	function getDescription()
	{
		return $this->description;
	}


	function hasDefaultValue()
	{
		return $this->useDefaultValue;
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
	function getType()
	{
		return '-';
	}
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


	function getType()
	{
		return $this->type->getName();
	}

}


class TextOptionItem extends OptionItem
{
	function getType()
	{
		return 'text';
	}
}


class IntOptionItem extends OptionItem
{

	function parse($val)
	{
		Validators::assert($val, 'numericint', "`--{$this->getName()}'");
		return (int)$val;
	}


	function getType()
	{
		return 'int';
	}


}


class FloatOptionItem extends OptionItem
{

	function parse($val)
	{
		Validators::assert($val, 'numeric', "`--{$this->getName()}'");
		return (float)$val;
	}


	function getType()
	{
		return 'float';
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
	function getType()
	{
		return 'bool';
	}
}
