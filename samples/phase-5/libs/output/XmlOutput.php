<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


/**
 * Výstup v XML formátu.
 */
class XmlOutput implements Output
{

	private $stream;
	private $root;
	private $started = false;
	private $formaters = [];
	private $parser;


	function __construct(Stream $stream, $root = 'output')
	{
		$this->root = $root;
		$this->stream = $stream;
		$this->parser = new MarkParser();
	}



	function __destruct()
	{
		if ($this->started) {
			$this->stream->write("</{$this->root}>\n");
		}
	}



	function notice($content)
	{
		if ( ! $this->started) {
			$this->started = true;
			$this->stream->write("<{$this->root}>\n");
		}
		$this->stream->write(self::format('notice', $content));
	}



	function error($content)
	{
		if ( ! $this->started) {
			$this->started = true;
			$this->stream->write("<{$this->root}>\n");
		}
		$this->stream->write(self::format('error', $content));
	}



	/**
	 * @param string notice | warning | error
	 * @param string
	 * @return string
	 */
	function format($type, $content)
	{
		if ($content === Null) {
			return "\t<{$type} type=\"Null\" />\n";
		}
		elseif (is_bool($content)) {
			return "\t<{$type} type=\"" . ($content ? 'True' : 'False') . "\" />\n";
		}
		elseif (is_scalar($content)) {
			return $this->formatText($type, $content);
		}
		elseif ($content instanceof Data) {
			return $this->formatData($type, $content);
		}
		else {
			return $this->formatText($type, '! invalid output: `' . get_class($content) . '\'');
		}
	}



	/**
	 * @param string
	 * @return string
	 */
	function translate($s)
	{
		$s = self::concat($this->parser->parse($s));
		$s = str_replace('\\<', '&lt;', $s);
		$s = htmlspecialchars(preg_replace('#[\x00-\x08\x0B\x0C\x0E-\x1F]+#', '', $s), ENT_QUOTES);
		return $s;
	}



	/**
	 * @param string notice | warning | error
	 * @param string
	 * @return string
	 */
	private function formatText($type, $content)
	{
		$content = $this->translate($content);
		return "\t<{$type}>{$content}</{$type}>\n";
	}



	/**
	 * @param string notice | warning | error
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
			$this->formaters['list'] = new ListDataXmlFormat($this);
		}
		return $this->formaters['list'];
	}



	private function getDictDataFormater()
	{
		if (empty($this->formaters['dict'])) {
			$this->formaters['dict'] = new DictDataXmlFormat($this);
		}
		return $this->formaters['dict'];
	}



	private function getTableDataFormater()
	{
		if (empty($this->formaters['table'])) {
			$this->formaters['table'] = new TableDataXmlFormat($this);
		}
		return $this->formaters['table'];
	}



	/**
	 * @return string
	 */
	private static function concat($xs)
	{
		$ret = '';
		foreach ($xs as $x) {
			if (is_object($x)) {
				$x = self::concat($x->content);
			}
			$ret .= $x;
		}
		return $ret;
	}

}
