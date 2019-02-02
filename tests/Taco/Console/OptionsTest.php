<?php
/**
 * Copyright (c) since 2004 Martin Takáč
 * @author Martin Takáč <martin@takac.name>
 */

namespace Taco\Console;

use PHPUnit_Framework_TestCase;


/**
 * @call phpunit OptionsTest.php
 */
class OptionsTest extends PHPUnit_Framework_TestCase
{


	function testEmptyIsCorrect()
	{
		$opt = new Options([]);
	}



	function testSameValues()
	{
		$opt = new Options(['name' => 'John', 'surname' => 'Dee', 'bool' => false, 'empty' => Null]);
		$this->assertSame('John', $opt->getOption('name'));
		$this->assertSame('Dee', $opt->getOption('surname'));
		$this->assertFalse($opt->getOption('bool'));
		$this->assertNull($opt->getOption('empty'));
		$this->assertEquals([
			'name' => 'John',
			'surname' => 'Dee',
			'bool' => false,
			'empty' => Null
		], $opt->asArray());
	}



	function testFromArrayFailBecauseUnusedRequireOptions()
	{
		$this->setExpectedException('InvalidArgumentException', "Option `foo' not found.");

		$opt = new Options([]);
		$opt->getOption('foo');
	}

}
