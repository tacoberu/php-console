<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


use InvalidArgumentException;


/**
 * Výcefázové zpracovávání vstupních parametrů. Vzor, kdy je možné získat
 * i jen část dat, a na základě nich dodat další validaci.
 */
class Request
{

	/**
	 * Jméno aplikace.
	 */
	private $program;


	/**
	 * Nezpracovaná data.
	 */
	private $data = array();


	/**
	 * Hodnoty argumentů.
	 */
	private $args = array();


	/**
	 * Na jaképozici jsme co se argumentů týče?
	 */
	private $position = 0;


	/**
	 * Zásobník pravidel.
	 */
	private $signature;


	function __construct($program)
	{
		$this->program = $program;
		$this->signature = new OptionSignature();
	}



	/**
	 *	Přiřadit nezpracovaná data.
	 */
	function addRawData(array $data)
	{
		$this->data = $data;
	}



	/**
	 * Na aktuální request uplatnit nějakou signaturu. Výsledkem bude částečně
	 * uspořádaný seznam optionů.
	 */
	function applyRules(OptionSignature $signature)
	{
		// Zmergujem signaturu
		$this->signature->merge($signature);

		// Defaultní hodnoty
		$this->args = array_merge($signature->getDefaultValues(), $this->args);

		// Vyzobat
		$tail = array();
		$args = $this->data;
		while (count($args) && $item = array_shift($args)) {
			// Klíč je jméno?
			if (self::isOptionFormat($item)) {
				if ($opt = $this->signature->getOption($item)) {
					$values = array_splice($args, 0, $opt->getValence());
					if ($opt->getValence() == 1) {
						$values = reset($values);
					}
					$this->args[$opt->getName()] = $values;
				}
				// Nevíme co jsou další data zač, možná je známe, možná ne.
				else {
					$tail[] = $item;
					foreach ($args as $item) {
						$tail[] = $item;
					}
					break;
				}
			}
			else {
				if ($opt = $this->signature->getOptionAt($this->position)) {
					$this->position++;
					// a co valence?
					$this->args[$opt->getName()] = $item;
				}
				// Nevíme co jsou další data zač, možná je známe, možná ne.
				else {
					$tail[] = $item;
					foreach ($args as $item) {
						$tail[] = $item;
					}
					break;
				}
			}
		}

		$this->data = $tail;
	}



	function isMissingRules()
	{
		return (bool)count($this->data);
	}



	/**
	 * @param string
	 */
	function getOption($name)
	{
		if (!array_key_exists($name, $this->args)) {
			throw new InvalidArgumentException("Option `$name' is not found.");
		}
		return $this->args[$name];
	}



	function getOptions()
	{
		return $this->args;
	}



	/**
	 * @return bool
	 */
	private static function isOptionFormat($name)
	{
		return ($name{0} == '-');
	}

}
