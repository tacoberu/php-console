<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


/**
 * <table>
 *   <tr>
 *     <th></th>
 *     <th></th>
 *     <th></th>
 *   </tr>
 *   <tr>
 *     <td></td>
 *     <td></td>
 *     <td></td>
 *   </tr>
 * </table>
 */
class TableDataXmlFormat
{


	private $output;


	function __construct($output)
	{
		$this->output = $output;
	}



	function format($type, TableData $msg)
	{
		$ret = "<table>\n";

		if (count($msg->getHeaders())) {
			$ret .= "\t<tr>\n";
			foreach ($msg->getHeaders() as $item) {
				$item = $this->translate($type, $item);
				$ret .= self::formatHeader($item);
			}
			$ret .= "\t</tr>\n";
		}

		foreach ($msg->getItems() as $items) {
			$ret .= "\t<tr>\n";
			foreach ($items as $item) {
				$item = $this->translate($type, $item);
				$ret .= self::formatRow($item);
			}
			$ret .= "\t</tr>\n";
		}

		$ret .= "</table>\n";
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



	private static function formatHeader($item)
	{
		return "\t\t<th>{$item}</th>\n";
	}



	private static function formatRow($item)
	{
		return "\t\t<td>{$item}</td>\n";
	}

}
