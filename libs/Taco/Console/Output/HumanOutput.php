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


	function __construct(Stream $stream)
	{
		$this->stream = $stream;
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
		return $s;
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
		die('dopsat formátování speciálních tříd jako je tabulka, seznam, a podobně.');
	}

}
