<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


use DomainException;


/**
 * Popis vstupních parametrů tasku. Jaké má povinné argumenty. Jaké volitelné,
 * s defaultními hodnotami.
 */
class OptionSignature
{


	const TYPE_TEXT = 'text';


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


	/**
	 * Odkazy na prvky pod jméném.
	 */
	private $options = array();


	/**
	 * Odkazy na prvky, pojmenované zkratkou. Například -a -b. Zkratky se dají
	 * seskupovat, takže -abc se rozpadne na -a -b -c
	 */
	private $shortcut = array();


	/**
	 * Odkazy na prvky, které jsou definované pozičně.
	 */
	private $positions = array();


	/**
	 * Povinný argument.
	 * @param string Jméno.
	 * @param ? Validační typ: string | int | float | bool | enum | set
	 * @param string Popis.
	 */
	function addArgument($name, $type, $description)
	{
		$par = explode('|', $name, 2);
		//~ $this->assertUniqueName($par[0]);
		$this->options[$par[0]] = $this->createOptionItemByType($type, $par[0], $description);
		if (isset($par[1])) {
			$this->options[$par[0]]->setShortname($par[1]);
			$this->shortcut[$par[1]] = $this->options[$par[0]];
		}
		return $this;
	}



	/**
	 * Volitelné nastavení
	 * @param string Jméno.
	 * @param ? Validační typ: string | int | float | bool | enum | set
	 * @param mixin Defaultní hodnota.
	 * @param string Popis.
	 */
	function addOptional($name, $defaultvalue, $type, $description)
	{
		$par = explode('|', $name, 2);
		//~ $this->assertUniqueName($par[0]);
		$this->options[$par[0]] = $this->createOptionItemByType($type, $par[0], $description)
				->setDefaultValue($defaultvalue);
		if (isset($par[1])) {
			$this->options[$par[0]]->setShortname($par[1]);
			$this->shortcut[$par[1]] = $this->options[$par[0]];
		}
		return $this;
	}



	/**
	 * Zkratka pro bool optional s defaultem na false.
	 */
	function addFlag($name, $description)
	{
		//~ $this->assertUniqueName($name);
		return $this->addOptional($name, False, self::TYPE_BOOL, $description);
	}



	/**
	 * Poziční jsou sice mimo klasické argumenty, ale všechna jména jsou v
	 * rámci signatury jedinečná.
	 */
	function addPositional($name, $type, $description)
	{
		//~ $this->assertUniqueName($name);
		$index = count($this->positions);
		$this->positions[] = $this->createOptionItemByType($type, $name, $description);
		$this->options[$name] = $index;
		return $this;
	}



	/**
	 * @param string $name Jméno optionu včetně případných pomlček.
	 * @return OptionItem
	 */
	function getOption($name)
	{
		$name = trim($name, ' "-_');
		if (isset($this->options[$name])) {
			$opt = $this->options[$name];
		}
		elseif (isset($this->shortcut[$name])) {
			$opt = $this->shortcut[$name];
		}

		if (isset($opt) && $opt instanceof OptionItem) {
			return $opt;
		}

		return Null;
	}



	function hasPositional()
	{
		return (bool)count($this->positions);
	}



	function getOptionAt($index)
	{
		if (isset($this->positions[$index])) {
			return $this->positions[$index];
		}
		return Null;
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
			if ($opt->hasDefaultValue()) {
				$xs[$opt->getName()] = $opt->getDefaultValue();
			}
		}
		return $xs;
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
