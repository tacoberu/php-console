<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


/**
 * Example:
 * +---------------+-----------------------+------------------+
 * | ISBN          | Title                 | Author           |
 * +---------------+-----------------------+------------------+
 * | 99921-58-10-7 | Divine Comedy         | Dante Alighieri  |
 * | 9971-5-0210-0 | A Tale of Two Cities  | Charles Dickens  |
 * | 960-425-059-0 | The Lord of the Rings | J. R. R. Tolkien |
 * +---------------+-----------------------+------------------+
 */
class TableData implements Data
{

    /**
     * Table headers.
     *
     * @var array
     */
    private $headers = [];


    /**
     * Table rows.
     *
     * @var array
     */
    private $items = [];


	static function create($headers = [])
	{
		return new self($headers);
	}


	function __construct($headers)
	{
		$this->headers = $headers;
	}


	function addRow($items)
	{
		$this->items[] = $items;
		return $this;
	}


	/**
	 * @return array
	 */
	function getHeaders()
	{
		return $this->headers;
	}


	/**
	 * @return array of arrays
	 */
	function getItems()
	{
		return $this->items;
	}

}
