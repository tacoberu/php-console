<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Commands;


use DomainException;


/**
 * Popis vstupních parametrů tasku. Jaké má povinné argumenty. Jaké volitelné,
 * s defaultními hodnotami.
 */
class OptionSignature
{


	const TYPE_TEXT = 'string';


	const TYPE_INT = 'int';


	const TYPE_FLOAT = 'float';


	const TYPE_BOOL = 'bool';


	static function TYPE_ENUM()
	{
		return new TypeEnum(func_get_args());
	}


	static function TYPE_SET()
	{
		return new TypeSet(func_get_args());
	}


	private $options = array();


	/**
	 * Povinný argument.
	 * @param string Jméno.
	 * @param ? Validační typ: string | int | float | bool | enum | set
	 * @param string Popis.
	 */
	function addArgument($name, $type, $description)
	{
		$this->options[$name] = $this->createOptionItemByType($type, $name, $description);
		return $this;
	}



	/**
	 * Volitelné nastavení
	 * @param string Jméno.
	 * @param ? Validační typ: string | int | float | bool | enum | set
	 * @param mixin Defaultní hodnota.
	 * @param string Popis.
	 */
	function addOption($name, $defaultvalue, $type, $description)
	{
		$this->options[$name] = $this->createOptionItemByType($type, $name, $description)
				->setDefaultValue($defaultvalue);
		return $this;
	}



	/**
	 * @param string $name Jméno optionu včetně případných pomlček.
	 * @return OptionItem
	 */
	function getOption($name)
	{
		$name = trim($name, ' "-_');
		return isset($this->options[$name]) ? $this->options[$name] : Null;
	}



	/**
	 * @return array of string
	 */
	function getOptionNames()
	{
		return array_keys($this->options);
	}



	/**
	 * @return array
	 */
	function getDefaultsValue()
	{
		$xs = array();
		foreach ($this->options as $opt) {
			$xs[$opt->getName()] = $opt->getDefaultValue();
		}
		return array_filter($xs);
	}



	function merge(self $with)
	{
		foreach ($with->options as $option) {
			$this->options[$option->getName()] = $option;
		}
		return $this;
	}



	// -- PRIVATE ------------------------------------------------------



	private function createOptionItemByType($type, $name, $description)
	{
		if ($type instanceof Type) {
			return new ConstraintOptionItem($type, $name, $description);
		}
		switch ($type) {
			case self::TYPE_TEXT:
				return new TextOptionItem($name, $description);
			case self::TYPE_INT:
				return new IntOptionItem($name, $description);
			case self::TYPE_FLOAT:
				return new FloatOptionItem($name, $description);
			case self::TYPE_BOOL:
				return new BoolOptionItem($name, $description);
			default:
				throw new DomainException("Unsupported type of option `$type'.");
		}
	}
}
