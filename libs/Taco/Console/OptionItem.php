<?php
/**
 * Copyright (c) since 2004 Martin Takáč
 * @author Martin Takáč <martin@takac.name>
 */

namespace Taco\Console;


/**
 * Volba aplikace.
 * Example `--name Name`.
 */
abstract class OptionItem
{

	/**
	 * Jméno volby: --name (bez pomlček).
	 * @var string:1..40
	 */
	private $name;


	/**
	 * Zkrácený název: -n (bez pomlček).
	 * @var string:1
	 */
	private $shortname;


	/**
	 * Důkladný popis.
	 * @var string:1..
	 */
	private $description;


	/**
	 * Defaultní hodnota.
	 */
	private $defaultValue;


	/**
	 * Obsahuje defaultní hodnotu? Defaultní hodnota může být i NULL
	 * nebo False, proto potřebujeme tento příznak.
	 * @var bool
	 */
	private $useDefaultValue = False;


	/**
	 * @param string:1..40
	 * @param string:1..
	 */
	function __construct($name, $description)
	{
		TypeUtils::assert($name, 'string:1..40');
		TypeUtils::assert($description, 'string:1..');
		$this->name = $name;
		$this->description = $description;
	}



	/**
	 * @param string|int|float|closure
	 */
	function setDefaultValue($val)
	{
		$this->defaultValue = $val;
		$this->useDefaultValue = True;
		return $this;
	}



	/**
	 * @param string:1
	 */
	function setShortname($name)
	{
		TypeUtils::assert($name, 'string:1');
		$this->shortname = $name;
	}



	/**
	 * Zkrácený název: -n (bez pomlček).
	 * @return string:1
	 */
	function getShortname()
	{
		return $this->shortname;
	}



	/**
	 * Jméno volby: --name (bez pomlček).
	 * @return string:1..40
	 */
	function getName()
	{
		return $this->name;
	}



	/**
	 * @return Type
	 */
	abstract function getType();



	/**
	 * Důkladný popis: "Jméno projektu".
	 * @return string:1..
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



	function getDefaultValue(Request $r)
	{
		if (is_callable($this->defaultValue)) {
			$cb = $this->defaultValue;
			return $cb($r);
		}
		return $this->defaultValue;
	}



	function getValueFrom(Request $r)
	{
		if ($r->hasOption($this->getName())) {
			return $r->getOption($this->getName());
		}
		return $this->getDefaultValue($r);
	}



	/**
	 * Zpracování vstupní hodnoty na sanitovanou. Případně to řve, když je blbost.
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

	/**
	 * @param string:1..40
	 * @param string:1..
	 */
	function __construct($name, $description)
	{
		parent::__construct($name, $description);
		$this->setDefaultValue(False);
	}



	function getType()
	{
		return new TypeBool();
	}



	/**
	 * Zpracování vstupní hodnoty na sanitovanou. Případně to řve, když je blbost.
	 */
	function parse($val)
	{
		return !$val;
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



	/**
	 * @return Type
	 */
	function getType()
	{
		return $this->type;
	}

}
