<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč <martin@takac.name>
 */

namespace Taco\Console;


use InvalidArgumentException,
	RuntimeException,
	UnexpectedValueException;


/**
 * Reprezentace autora.
 */
class Author
{

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $email;

	/**
	 * @param string "Name Surname", "Name Surname <name@surname.dom>"
	 */
	static function fromString($s)
	{
		if ( ! preg_match('~^([^@]+)(\<[^>]+@[^>]+>)?$~', trim($s), $matches)) {
			throw new UnexpectedValueException();
		}
		if (count($matches) == 3) {
			return new self(trim($matches[1]), trim($matches[2], ' <>'));
		}
		if (count($matches) == 2) {
			return new self(trim($matches[1]));
		}
		throw new UnexpectedValueException();
	}



	/**
	 * @param string "John Dee"
	 * @param string "johndee@uk.com"
	 */
	function __construct($name, $email = null)
	{
		$this->name = $name;
		$this->email = $email;
	}



	function __toString()
	{
		if ($this->email) {
			return "{$this->name} <{$this->email}>";
		}
		return "{$this->name}";
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
	function getEmail()
	{
		return $this->email;
	}


}
