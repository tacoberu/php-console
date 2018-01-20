<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


/**
 * Výstup
 */
class HumanOutput implements Output
{

	private $stream;
	private $colors;
	private $parser;
	private $formaters = [];


	function __construct(Stream $stream, $colors = Null)
	{
		$this->stream = $stream;
		$this->colors = new Colors();
		$this->parser = new MarkParser();
	}



	function notice($content)
	{
		$this->stream->write(self::format('notice', $content));
	}



	function error($content)
	{
		$this->stream->write(self::format('error', $content));
	}



	/**
	 * @param string notice | warning | error
	 * @param mixed
	 * @return string
	 */
	function format($type, $content)
	{
		if ($content === Null) {
			return self::formatText($type, 'Null');
		}
		elseif (is_bool($content)) {
			return self::formatText($type, $content ? 'True' : 'False');
		}
		elseif (is_scalar($content)) {
			return self::formatText($type, $content);
		}
		elseif ($content instanceof Data) {
			return $this->formatData($type, $content) . PHP_EOL;
		}
		else {
			return self::formatText($type, '! invalid output: `' . get_class($content) . '\'');
		}
	}



	/**
	 * @param string
	 * @return string
	 */
	function translate($s)
	{
		return self::concat($this->colors, $this->parser->parse($s));
	}



	/**
	 * @param string notice | warning | error
	 * @param string
	 * @return string
	 */
	private function formatText($type, $content)
	{
		return $this->translate($content) . "\n";
	}



	/**
	 * @param string notice | warning | error
	 * @param Data
	 * @return string
	 */
	private function formatData($type, Data $content)
	{
		switch(True) {
			case $content instanceof ListData:
				return $this->getListDataFormater()->format($type, $content);
			case $content instanceof DictData:
				return $this->getDictDataFormater()->format($type, $content);
			case $content instanceof TableData:
				return $this->getTableDataFormater()->format($type, $content);
			default:
				die('dopsat formátování speciálních tříd jako je tabulka, seznam, a podobně.');
		}
	}



	private function getListDataFormater()
	{
		if (empty($this->formaters['list'])) {
			$this->formaters['list'] = new ListDataHumanFormat($this);
		}
		return $this->formaters['list'];
	}



	private function getDictDataFormater()
	{
		if (empty($this->formaters['dict'])) {
			$this->formaters['dict'] = new DictDataHumanFormat($this);
		}
		return $this->formaters['dict'];
	}



	private function getTableDataFormater()
	{
		if (empty($this->formaters['table'])) {
			$this->formaters['table'] = new TableDataHumanFormat($this);
		}
		return $this->formaters['table'];
	}



	/**
	 * @return string
	 */
	private static function concat($colors, $xs)
	{
		$ret = '';
		foreach ($xs as $x) {
			if (is_object($x)) {
				$style = (object)array_merge(['fg' => Null, 'bg' => Null, 'opt' => Null], (array)$x->style);
				$x = $colors->apply(self::concat($colors, $x->content), $style->fg, $style->bg, $style->opt);
			}
			$ret .= $x;
		}
		return $ret;
	}

}
