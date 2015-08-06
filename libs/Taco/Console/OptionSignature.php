<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


use DomainException,
	LogicException;


/**
 * Popis vstupních parametrů programu. Jaké má povinné argumenty. Jaké volitelné,
 * s defaultními hodnotami. Jako argumentem může být i další signature, a tak to zanořovat do stromu.
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
	 * Optiony, které jsou pozicované.-
	 */
	private $arguments = array();


	/**
	 * Optiony, které jdou mimo, a musí se vždy explicitně pojmenovat.
	 */
	private $options = array();


	/**
	 * Odkazy na prvky, pojmenované zkratkou. Například -a -b. Zkratky,
	 * kterým se nenastavuje hodnota se dají seskupovat, takže -abc
	 * se rozpadne na -a -b -c
	 */
	private $shortcut = array();



	/**
	 * Povinný argument bez defaultní hodnoty. Vždy musí být vyplněn.
	 *
	 * @param string Jméno.
	 * @param ? Validační typ: string | int | float | bool | enum | set
	 * @param string Popis.
	 */
	function addArgument($name, $type, $description)
	{
		$par = explode('|', $name, 2);
		$this->assertUniqueArgument($par[0]);
		$this->arguments[$par[0]] = $this->createOptionItemByType($type, $par[0], $description);
		if (isset($par[1])) {
			$this->arguments[$par[0]]->setShortname($par[1]);
			$this->shortcut[$par[1]] = $this->arguments[$par[0]];
		}
		return $this;
	}



	/**
	 * Povinný argument s defaultní hodnotout. Nemusí být použit.
	 *
	 * @param string Jméno.
	 * @param ? Validační typ: string | int | float | bool | enum | set
	 * @param string Popis.
	 */
	function addArgumentDefault($name, $type, $defaultvalue, $description)
	{
		$par = explode('|', $name, 2);
		$this->assertUniqueArgument($par[0]);
		//~ $this->assertNotUseOptional();
		$this->arguments[$par[0]] = $this->createOptionItemByType($type, $par[0], $description)
				->setDefaultValue($defaultvalue);
		if (isset($par[1])) {
			$this->arguments[$par[0]]->setShortname($par[1]);
			$this->shortcut[$par[1]] = $this->arguments[$par[0]];
		}
		return $this;
	}



	/**
	 * Volitelné nastavení mimo pořadí.
	 *
	 * @param string Jméno.
	 * @param ? Validační typ: string | int | float | bool | enum | set
	 * @param mixin Defaultní hodnota.
	 * @param string Popis.
	 */
	function addOption($name, $type, $defaultvalue, $description)
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
		return $this->addOption($name, self::TYPE_BOOL, False, $description);
	}



	/**
	 * @param string $name Jméno optionu včetně případných pomlček.
	 * @return OptionItem
	 */
	function getOption($name)
	{
		$name = trim($name, ' "-_');
		if (isset($this->arguments[$name])) {
			$opt = $this->arguments[$name];
		}
		elseif (isset($this->options[$name])) {
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



	/**
	 * @param int $index
	 */
	function getOptionAt($index)
	{
		foreach ($this->arguments as $item){
			if ($index-- == 0) {
				return $item;
			}
		}
		return Null;
	}



	/**
	 * Seznam všech názvů argumentů.
	 * @return array of string
	 */
	function getOptionNames()
	{
		return array_merge(array_keys($this->arguments),
			array_keys($this->options));
	}



	/**
	 * Seznam všech defaultních hodnot.
	 * @return array
	 */
	function getDefaultValues()
	{
		$xs = array();
		foreach ($this->arguments as $opt) {
			if ($opt->hasDefaultValue()) {
				$xs[$opt->getName()] = $opt->getDefaultValue();
			}
		}
		foreach ($this->options as $opt) {
			if ($opt->hasDefaultValue()) {
				$xs[$opt->getName()] = $opt->getDefaultValue();
			}
		}
		return $xs;
	}




	function merge(self $with)
	{
		foreach ($with->arguments as $option) {
			$this->arguments[$option->getName()] = $option;
		}
		foreach ($with->options as $option) {
			$this->options[$option->getName()] = $option;
		}
		foreach ($with->shortcut as $name => $option) {
			$this->shortcut[$name] = $option;
		}
		return $this;
	}




	// -- PRIVATE ------------------------------------------------------



	private function createOptionItemByType($type, $name, $description)
	{
		switch ($type) {
			case self::TYPE_TEXT:
				$type = new TypeText();
				break;
			case self::TYPE_INT:
				$type = new TypeInt();
				break;
			case self::TYPE_FLOAT:
				$type = new TypeFloat();
				break;
			case self::TYPE_BOOL:
				$type = new TypeBool();
				break;
		}

		if ($type instanceof Type) {
			return new ConstraintOptionItem($type, $name, $description);
		}

		throw new DomainException("Unsupported type of option `$type'.");
	}



	private function assertUniqueArgument($name)
	{
		if (isset($this->arguments[$name])) {
			new LogicException("Argument with name `$name' has been used.");
		}
	}


/*
	private function assertNotUseOptional()
	{
		if (empty($this->arguments)) {
			return True;
		}

		dump($this->arguments[]);

		if (isset($this->arguments[$name])) {
			new LogicException("Argument with name `$name' has been used.");
		}
	}
	*/
}
