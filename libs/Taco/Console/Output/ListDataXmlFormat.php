<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


/**
 * <list label="foo">
 *   <item>boo</item>
 *   <item>foo</item>
 *   <item>goo</item>
 * </list>
 */
class ListDataXmlFormat
{

	private $output;


	function __construct($output)
	{
		$this->output = $output;
	}



	function format($type, ListData $msg)
	{
		$pad = 2;
		if ($msg->getGroupName()) {
			$label = $this->translate($type, $msg->getGroupName());
			$ret = "<list label=\"{$label}\">\n";
		}
		else {
			$ret = "<list>\n";
		}

		foreach ($msg->getItems() as $row) {
			$row = $this->translate($type, $row);
			$ret .= "\t<item>{$row}</item>\n";
		}

		$ret .= "</list>\n";
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

}
