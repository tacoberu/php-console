<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


/**
 * foo:
 *   a: boo
 *   b: too
 *   c: goo
 */
class DictDataHumanFormat
{

	private $output;


	function __construct($output)
	{
		$this->output = $output;
	}



	/**
	 * @return string
	 */
	function format($type, DictData $msg)
	{
		$ret = $this->translate($type, $msg->getGroupName());
		if (! empty($ret)) {
			$ret .= PHP_EOL;
		}
		if (empty($msg->getItems())) {
			return '';
		}
		$pad = strlen(max(array_map(function($x) { return $x[0]; }, $msg->getItems()))) + 1;
		foreach ($msg->getItems() as $row) {
			$ret .= sprintf("  %-{$pad}s %s\n", $this->translate($type, $row[0]), $this->translate($type, $row[1]));
			if (isset($row[2])) {
				$ret .= self::indent($this->output->format($type, $row[2]));
			}
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
