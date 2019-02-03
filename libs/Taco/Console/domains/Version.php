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
 * Reprezentace verze.
 */
class Version
{

	/**
	 * @var int
	 */
	private $major;

	/**
	 * @var int
	 */
	private $minor;

	/**
	 * @var int
	 */
	private $release;


	/**
	 * @param string "1.2.3", 1.2-3"
	 */
	static function fromString($s)
	{
		$xs = explode('.', $s);
		if (count($xs) == 3) {
			return new self($xs[0], $xs[1], $xs[2]);
		}
		if (count($xs) == 2) {
			$sec = explode('-', $xs[1]);
			if (count($sec) == 2) {
				return new self($xs[0], $sec[0], $sec[1]);
			}
		}
		throw new UnexpectedValueException();
	}



	/**
	 * @param int
	 * @param int
	 * @param int
	 */
	function __construct($major, $minor, $release)
	{
		$this->major = (int)$major;
		$this->minor = (int)$minor;
		$this->release = (int)$release;
	}



	function __toString()
	{
		return "{$this->major}.{$this->minor}-{$this->release}";
	}



	/**
	 * @return int
	 */
	function getMajor()
	{
		return $this->major;
	}



	/**
	 * @return int
	 */
	function getMinor()
	{
		return $this->minor;
	}



	/**
	 * @return int
	 */
	function getRelease()
	{
		return $this->release;
	}

}
