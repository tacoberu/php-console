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
class RequestX
{

	private $data;
	private $pwd;
	private $program;
	private $command;
	private $args;


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
		$tail = array();
		$args = $this->data;
		$position = 0;
		while (count($args) && $item = array_shift($args)) {
			if (self::isOptionFormat($item) && ($opt = $signature->getOption($item))) {
				$values = array_splice($args, 0, $opt->getValence());
				if ($opt->getValence() == 1) {
					$values = reset($values);
				}
				$this->args[$opt->getName()] = $values;
			}
			elseif ($signature->hasPositional()) {
				if (! $opt = $signature->getOptionAt($position)) {
					throw new InvalidArgumentException("Chybí poziční argument na indexu `$position'.");
				}
				print_r($item);
				print_r($opt);
				die('=====[' . __line__ . '] ' . __file__);
			}
			else {
				$tail[] = $item;
			}
		}
		$this->data = $tail;
	}



	function hasCommandName()
	{
		try {
			return (bool)$this->getCommandName();
		}
		catch (InvalidArgumentException $e) {
			return False;
		}
	}



	function setCommandName($m)
	{
		$this->command = $m;
		return $this;
	}



	function getCommandName()
	{
		if (empty($this->command)) {
			$command = array_shift($this->data);
			if (self::isOptionFormat($command)) {
				throw new InvalidArgumentException('Jméno akce musí být před všemi jejími argumenty.');
			}
			$this->command = $command;
		}
		return $this->command;
	}



	function isMissingRules()
	{
		return (bool)count($this->data);
	}



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
