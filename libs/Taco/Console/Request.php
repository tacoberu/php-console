<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


use InvalidArgumentException,
	RuntimeException,
	UnexpectedValueException;


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
	 * Na jaké pozici jsme co se argumentů týče?
	 */
	private $position = 0;


	/**
	 * Které poziční už byly použity. Musí se přeskakovat.
	 */
	private $skiped = array();


	/**
	 * Zásobník pravidel.
	 */
	private $signature;



	/**
	 * @param string $program
	 */
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
				if (strpos($item, '=')) {
					list($item, $back) = explode('=', $item, 2);
					array_unshift($args, $back);
				}
				if ($opt = $this->signature->getOption($item)) {
					$index = $this->signature->getIndexOfOption($opt->getName());
					// Je to poziční, a...
					if ($index >= 0) {
						// Právě jsi na správné pozici, jméno je nadbytečné. Ale jsme ve špatné podmínce.
						if ($index == $this->position) {
							$this->position++;
						}
						// Mimo pořadí, musím to trochu zamíchat...
						elseif ($index > $this->position) {
							$this->skiped[] = $opt->getName();
						}
					}

					$values = array_splice($args, 0, $opt->getValence());
					if ($opt->getValence() == 1) {
						$values = reset($values);
					}
					try {
						$this->args[$opt->getName()] = $opt->parse($values);
					}
					catch (TypeException $e) {
						throw new UnexpectedValueException("Option `{$opt->getName()}' has invalid type of value: `{$e->getValue()}'. Except type: `{$e->getTypeName()}'.", 1, $e);
					}
				}
				// Nevíme co jsou další data zač, možná je známe, možná ne. Konec.
				else {
					$tail[] = $item;
					foreach ($args as $item) {
						$tail[] = $item;
					}
					break;
				}
			}
			// Poziční, nebo nerozhodnutelný.
			else {
				// Přeskočit už použité.
				while (($opt = $this->signature->getOptionAt($this->position))
						&& in_array($opt->getName(), $this->skiped)) {
					$this->position++;
				}

				// Poziční
				if ($opt) {
					$this->position++;
					// a co valence?
					try {
						$this->args[$opt->getName()] = $opt->parse($item);
					}
					catch (TypeException $e) {
						throw new UnexpectedValueException("Option `{$opt->getName()}' has invalid type of value: `{$e->getValue()}'. Except type: `{$e->getTypeName()}'.", 1, $e);
					}
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



	/**
	 * Zda jsou uplatněny všechna pravidla.
	 * @return boolean
	 */
	function isMissingRules()
	{
		return (bool)count($this->data);
	}



	/**
	 * Zda jsou naplněny všechny povinné prvky.
	 * @return boolean
	 */
	function isFilled()
	{
		$xs = array_diff($this->signature->getOptionNames(),
				array_keys($this->args));
		return empty($xs);
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
		if (! $this->isFilled()) {
			$xs = array_diff($this->signature->getOptionNames(),
					array_keys($this->args));
			$res = [];
			foreach ($xs as $x) {
				$res[] = self::formatOption($this->signature->getOption($x));
			}
			throw new RuntimeException("Missing required options:\n" . implode("\n", $res) . "");
		}
		return new Options($this->args);
	}



	// -- PRIVATE ------------------------------------------------------



	/**
	 * @return bool
	 */
	private static function formatOption(OptionItem $opt)
	{
		return "  --{$opt->getName()}"
			. ($opt->getShortname() ? ", -{$opt->getShortname()}" : Null)
			. "  [{$opt->getType()}]"
			. "  {$opt->getDescription()}"
			;
	}



	/**
	 * @return bool
	 */
	private static function isOptionFormat($name)
	{
		return ($name{0} == '-');
	}

}
