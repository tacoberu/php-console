<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Commands;


use InvalidArgumentException;


/**
 * Seznam zpracovaných vstupních hodnot.
 */
class Options
{

	/**
	 * volba a hodnota předaná z CLI.
	 */
	private $options = array();


	/**
	 * volba a defaultní hodnota získaná z definice.
	 */
	private $defaults = array();


	/**
	 * @param string $name Jméno parametru.
	 * @param mixin $default Defaultní hodnota, není-li uvedena.
	 * @return mixin.
	 */
	function getOption($name, $default = Null)
	{
		if (isset($this->options[$name])) {
			return $this->options[$name];
		}

		if ($default) {
			return $default;
		}

		if (isset($this->defaults[$name])) {
			return $this->defaults[$name];
		}

		throw new InvalidArgumentException("Option `$name' not found.");
	}


	/**
	 * Zpracuje vstupní pole stringů a vybere z nich parametry.
	 */
	static function fromArray(array $args, OptionSignature $signature)
	{
		$inst = new static();
		$inst->defaults = $signature->getDefaultsValue();
		while (count($args) && $name = array_shift($args)) {
			self::assertOptionFormat($name);

			if (! $opt = $signature->getOption($name)) {
				throw new InvalidArgumentException("Option `$name' not found.");
			}
			else if ($opt instanceof FlagOptionItem) {
				$inst->options[$opt->getName()] = True;
			}
			else {
				if (!count($args)) {
					throw new InvalidArgumentException("Missing value of option `$name'.");
				}
				$inst->options[$opt->getName()] = $opt->parse(array_shift($args));
			}
		}

		// Validace povinných hodnot.
		$missing = array_diff($signature->getOptionNames(), array_keys($inst->options), array_keys($inst->defaults));
		if (count($missing)) {
			throw new InvalidArgumentException("Option(s) `" . implode(', ', $missing) . "' are required.");
		}

		return $inst;
	}



	private static function assertOptionFormat($name)
	{
		if (empty($name) || $name{0} != '-' || $name{1} != '-') {
			throw new InvalidArgumentException("Option `$name' has invalid format. Must be prefixed of `--'.");
		}
		return $name;
	}

}
