<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


require_once __dir__ . '/../../../../vendor/autoload.php';
require_once __dir__ . '/../../../../libs/Taco/Console/interfaces.php';
require_once __dir__ . '/../../../../libs/Taco/Console/Output/MarkParser.php';


use PHPUnit_Framework_TestCase;



/**
 * @call phpunit MarkParserTest.php
 */
class MarkParserTest extends PHPUnit_Framework_TestCase
{

	private $parser;


	function setUp()
	{
		$this->parser = new MarkParser();
	}



	function testNoop()
	{
		$this->assertEquals(['noop'], $this->parser->parse('noop'));
	}



	function testEasy()
	{
		$text = 'Et <fg=green;bg=white>Lorem</> ipsum...';
		$this->assertEquals([
			'Et ',
			(object)[
				'content' => ['Lorem'],
				'style' => ['fg' => 'green', 'bg' => 'white']
			],
			' ipsum...',
		], $this->parser->parse($text));
	}



	function testManyMarks()
	{
		$text = 'Et <fg=green;bg=white>Lorem</> ipsum... <bg=gray>a na závěr</bg> tečka.';
		$this->assertEquals([
			'Et ',
			(object)[
				'content' => ['Lorem'],
				'style' => ['fg' => 'green', 'bg' => 'white']
			],
			' ipsum... ',
			(object)[
				'content' => ['a na závěr'],
				'style' => ['bg' => 'gray']
			],
			' tečka.',
		], $this->parser->parse($text));
	}



	function testManyMarksDeep()
	{
		$text = 'Et <fg=green;bg=white><fg=red> ! </fg>Lorem</> ipsum... <bg=gray>a na závěr</bg> tečka.';
		$this->assertEquals([
			'Et ',
			(object)[
				'style' => ['fg' => 'green', 'bg' => 'white'],
				'content' => [
					(object)[
						'style' => ['fg' => 'red'],
						'content' => [' ! ']
					],
					'Lorem',
				]
			],
			' ipsum... ',
			(object)[
				'content' => ['a na závěr'],
				'style' => ['bg' => 'gray']
			],
			' tečka.',
		], $this->parser->parse($text));
	}



	function testEscapeTags()
	{
		$text = 'Et \<command> ! \</command> tečka.';
		$this->assertEquals([
			'Et <command> ! </command> tečka.'
		], $this->parser->parse($text));
	}



	function testUnknowTagsShort()
	{
		$text = 'Et \<command> ! \</> tečka.';
		$this->assertEquals([
			'Et <command> ! </> tečka.'
		], $this->parser->parse($text));
	}



	function _testUnknowTagsShortX()
	{
		$text = 'Et \<command> ! </> tečka.';
		$this->assertEquals([
			'Et <command> ! </> tečka.'
		], $this->parser->parse($text));
	}



	function testEscapeTagsOne()
	{
		$text = 'Et \<bg=red> tečka.';
		$this->assertEquals([
			'Et <bg=red> tečka.'
		], $this->parser->parse($text));
	}



	function _testUnknowTags()
	{
		$text = 'Et <command> ! </command> tečka.';
		$this->assertEquals([
			'Et <command> ! </command> tečka.'
		], $this->parser->parse($text));
	}



	function _testUnknowTagsShort()
	{
		$text = 'Et <command> ! </> tečka.';
		$this->assertEquals([
			'Et <command> ! </> tečka.'
		], $this->parser->parse($text));
	}



	function _testUnknowTagsOne()
	{
		$text = 'Et <command> tečka.';
		$this->assertEquals([
			'Et <command> ! tečka.'
		], $this->parser->parse($text));
	}



	/**
	 * @return string
	 */
	private static function concat($colors, $xs)
	{
		$ret = '';
		foreach ($xs as $x) {
			if (is_object($x)) {
				$style = (object)array_merge(['fg' => Null, 'bg' => Null, 'opt' => NUll], $x->style);
				$x = $colors->apply(self::concat($colors, $x->content), $style->fg, $style->bg, $style->opt);
			}
			$ret .= $x;
		}
		return $ret;
	}


}
