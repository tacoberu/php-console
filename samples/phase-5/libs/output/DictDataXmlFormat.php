<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


/**
 * <dict label="foo">
 *   <item label="a">boo</item>
 *   <item label="b">foo</item>
 *   <item label="c">goo</item>
 * </dict>
 */
class DictDataXmlFormat
{

	private $output;


	function __construct($output)
	{
		$this->output = $output;
	}



	function format($type, DictData $msg)
	{
		$pad = 2;
		if ($msg->getGroupName()) {
			$label = $this->translate($type, $msg->getGroupName());
			$ret = "<dict label=\"{$label}\">\n";
		}
		else {
			$ret = "<dict>\n";
		}

		foreach ($msg->getItems() as $row) {
			$label = $this->translate($type, $row[0]);
			$text = $this->translate($type, $row[1]);
			if ($row[2]) {
				$ret .= "\t<item label=\"{$label}\">{$text}\n";
				$ret .= self::indent($this->format($type, $row[2]), 2);
				$ret .= "\t</item>\n";
			}
			else {
				$ret .= "\t<item label=\"{$label}\">{$text}</item>\n";
			}
		}

		$ret .= "</dict>\n";
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
	private static function indent($s, $level = 1, $chars = "\t")
	{
		if ($level > 0) {
			$s = preg_replace('#(?:^|[\r\n]+)(?=[^\r\n])#', '$0' . str_repeat($chars, $level), $s);
		}
		return $s;
	}

}
