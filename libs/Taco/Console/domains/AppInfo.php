<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč <martin@takac.name>
 */

namespace Taco\Console;


/**
 * Info about application.
 */
class AppInfo
{

	private $name;

	private $description;

	private $epilog;


	/**
	 * @param string
	 * @param string
	 * @param string
	 */
	function __construct($name, $description, $epilog = Null)
	{
		$this->name = $name;
		$this->description = $description;
		$this->epilog = $epilog;
	}



	function getName()
	{
		return $this->name;
	}



	function getDescription()
	{
		return $this->description;
	}



	function getEpilog()
	{
		return $this->epilog;
	}

}
