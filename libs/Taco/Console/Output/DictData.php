<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


/**
 * foo:
 *   first:   boo
 *   last:    too
 *   seccond: goo
 */
class DictData implements Data
{

	private $groupName;
	private $items = [];


	static function create($groupName)
	{
		return new self($groupName);
	}


	function __construct($groupName)
	{
		$this->groupName = $groupName;
	}


	function add($title, $msg, $subgroup = Null)
	{
		$this->items[] = [$title, $msg, $subgroup];
		return $this;
	}


	function getGroupName()
	{
		return $this->groupName;
	}


	function getItems()
	{
		return $this->items;
	}

}
