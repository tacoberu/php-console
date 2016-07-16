<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


/**
 * Renders table to output.
 *
 * Example:
 * +---------------+-----------------------+------------------+
 * | ISBN          | Title                 | Author           |
 * +---------------+-----------------------+------------------+
 * | 99921-58-10-7 | Divine Comedy         | Dante Alighieri  |
 * | 9971-5-0210-0 | A Tale of Two Cities  | Charles Dickens  |
 * | 960-425-059-0 | The Lord of the Rings | J. R. R. Tolkien |
 * +---------------+-----------------------+------------------+
 */
class TableDataHumanFormat
{

	private $output;


	function __construct($output)
	{
		$this->output = $output;
	}


	function format($type, TableData $msg)
	{
		$height = 50;
		$ret = $hrule = '+' . str_pad('', $height, '-') . "+\n";
		if (count($msg->getHeaders())) {
			$row = [];
			foreach ($msg->getHeaders() as $item) {
				$row[] = self::formatHeader($this->translate($type, $item));
			}
			$ret .= '|' . join('|', $row) . "|\n";
		}

		foreach ($msg->getItems() as $items) {
			$row = [];
			foreach ($items as $item) {
				$row[] = self::formatRow($this->translate($type, $item));
			}
			$ret .= '|' . join('|', $row) . "|\n";
		}

		$ret .= $hrule;
		return $ret;
	}



	private function translate($type, $data)
	{
		if ($data instanceof Data) {
			return $this->output->format($data);
		}
		else {
			return $this->output->translate($data);
		}
	}



	private function formatHeader($item)
	{
		return " {$item} ";
	}



	private function formatRow($item)
	{
		return " {$item} ";
	}

}
