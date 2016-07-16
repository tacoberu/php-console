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


	function __construct(Stream $stream, $root = 'output')
	{
		$this->root = $root;
		$this->stream = $stream;
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
	 * @param string notice | warning | error
	 * @param string
	 * @return string
	 */
	private function formatText($type, $content)
	{
		return "\t<{$type}>{$content}</{$type}>\n";
	}



	/**
	 * @param string notice | warning | error
	 * @return string
	 */
	private function formatData($type, Data $content)
	{
		die('dopsat formátování speciálních tříd jako je tabulka, seznam, a podobně.');
	}

}
