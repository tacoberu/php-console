<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


use Nette\Utils\Validators;


/**
 * Volba, parametr. Napříklda `--name Name`.
 */
abstract class OptionItem
{

	/**
	 * Jméno volby.
	 * @var string
	 */
	private $name;


	/**
	 * Zkrácený název
	 * @var string
	 */
	private $shortname;


	/**
	 * Důkladný popis.
	 * @var string
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


	/**
	 * @param string
	 * @param string
	 */
	function __construct($name, $description)
	{
		Validators::assert($name, 'string:1..');
		Validators::assert($description, 'string:1..');
		$this->name = $name;
		$this->description = $description;
	}



	function setDefaultValue($text)
	{
		$this->defaultValue = $text;
		$this->useDefaultValue = True;
		return $this;
	}



	/**
	 * @param string
	 */
	function setShortname($name)
	{
		Validators::assert($name, 'string:1..');
		$this->shortname = $name;
	}



	/**
	 * @return string
	 */
	function getShortname()
	{
		return $this->shortname;
	}



	/**
	 * @return string
	 */
	function getName()
	{
		return $this->name;
	}



	/**
	 * @return string
	 */
	function getDescription()
	{
		return $this->description;
	}



	/**
	 * @return bool
	 */
	function hasDefaultValue()
	{
		return (bool)$this->useDefaultValue;
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


	/**
	 * Minimální a maximální počet prvků.
	 * @return int
	 */
	function getValence()
	{
		return 1;
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

	/**
	 * Minimální a maximální počet prvků.
	 * @return int
	 */
	function getValence()
	{
		return 0;
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
		if ($this->type instanceof TypeText) {
			return 'text';
		}
		if ($this->type instanceof TypeInt) {
			return 'int';
		}
		if ($this->type instanceof TypeFloat) {
			return 'float';
		}
		if ($this->type instanceof TypeBool) {
			return 'bool';
		}
		if ($this->type instanceof TypeText) {
			return 'text';
		}
		return strtr(get_class($this->type), '\\', '.');
	}

}
