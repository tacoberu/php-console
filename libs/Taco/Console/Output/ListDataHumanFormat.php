<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


/**
 * foo:
 * - boo
 * - too
 * - goo
 */
class ListDataHumanFormat
{

	private $output;


	function __construct($output)
	{
		$this->output = $output;
	}


	function format($type, ListData $msg)
	{
		$pad = 2;
		$ret = $this->translate($type, $msg->getGroupName());
		if (! empty($ret)) {
			$ret .= PHP_EOL;
			$pad = 4;
		}

		foreach ($msg->getItems() as $row) {
			$ret .= ' - ' . $this->translate($type, $row) . PHP_EOL;
		}

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



	/**
	 * @credict David Grudl
	 */
	private static function indent($s, $level = 2, $chars = " ")
	{
		if ($level > 0) {
			$s = preg_replace('#(?:^|[\r\n]+)(?=[^\r\n])#', '$0' . str_repeat($chars, $level), $s);
		}
		return $s;
	}

}
