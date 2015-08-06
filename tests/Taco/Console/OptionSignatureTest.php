<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


require_once __dir__ . '/../../../vendor/autoload.php';
require_once __dir__ . '/../../../libs/Taco/Console/RequestX.php';


use PHPUnit_Framework_TestCase;



/**
 * prog one two three
 * prog one two three -p
 * prog one two three --pro
 * prog one two three -p abc
 * prog one two three --pro abc
 * prog one two three --pro abc four five
 * prog one two three --pro abc --pro def
 *
 * @call phpunit OptionSignatureTest.php
 */
class OptionSignatureTest extends PHPUnit_Framework_TestCase
{


	function testRequiredArgument()
	{
		$sig = new OptionSignature();
		$sig->addArgument('name|n', $sig::TYPE_TEXT, 'Use name');
		$sig->addArgument('age', $sig::TYPE_INT, 'Use age');

		$this->assertEquals(array('name', 'age'), $sig->getOptionNames(), 'List of option names.');
		$this->assertEqualsOption('name', $sig::TYPE_TEXT, $sig->getOption('name'));
		$this->assertEqualsOption('name', $sig::TYPE_TEXT, $sig->getOption('--name'));
		$this->assertEqualsOption('name', $sig::TYPE_TEXT, $sig->getOption(' --name'));
		$this->assertEquals('n', $sig->getOption('name')->getShortname());
		$this->assertEquals($sig->getOption('n'), $sig->getOption('name'));
		$this->assertEqualsOption('age', $sig::TYPE_INT, $sig->getOption(' age'));
		$this->assertNull($sig->getOption('age')->getShortname());
		$this->assertFalse($sig->hasPositional());
	}



	function testOptionalArgument()
	{
		$sig = new OptionSignature();
		$sig->addOptional('show|s', True, $sig::TYPE_BOOL, 'Zobrazit volitelně.');
		$sig->addOptional('size', 42, $sig::TYPE_INT, 'Use age');

		$this->assertEquals(array('show', 'size'), $sig->getOptionNames(), 'List of option names.');
		$this->assertEqualsOption('show', $sig::TYPE_BOOL, $sig->getOption('show'));
		$this->assertEqualsOption('size', $sig::TYPE_INT, $sig->getOption('size'));
		$this->assertEquals('s', $sig->getOption('show')->getShortname());
		$this->assertEquals($sig->getOption('s'), $sig->getOption('show'));
	}



	/**
	 * shortcut for $sig->addOptional('show|s', True, $sig::TYPE_BOOL, 'Zobrazit volitelně.');
	 */
	function testFlagArgument()
	{
		$sig = new OptionSignature();
		$sig->addFlag('show|s', 'Zobrazit volitelně.');
		$sig->addFlag('size', 'Use age');

		$this->assertEquals(array('show', 'size'), $sig->getOptionNames(), 'List of option names.');
		$this->assertEqualsOption('show', $sig::TYPE_BOOL, $sig->getOption('show'));
		$this->assertEqualsOption('size', $sig::TYPE_BOOL, $sig->getOption('size'));
		$this->assertEquals('s', $sig->getOption('show')->getShortname());
		$this->assertEquals($sig->getOption('s'), $sig->getOption('show'));
	}



	function testPositionalArgument()
	{
		$sig = new OptionSignature();
		$sig->addPositional('name', $sig::TYPE_TEXT, 'poziční');
		$sig->addPositional('surname', $sig::TYPE_TEXT, 'poziční');

		$this->assertEquals(array('name', 'surname'), $sig->getOptionNames());
		$this->assertNull($sig->getOption('name'));
		$this->assertNull($sig->getOption('surname'));
		$this->assertEqualsOption('name', $sig::TYPE_TEXT, $sig->getOptionAt(0));
		$this->assertEqualsOption('surname', $sig::TYPE_TEXT, $sig->getOptionAt(1));
		$this->assertTrue($sig->hasPositional());
	}



	function testComplex()
	{
		$sig = new OptionSignature();
		$sig->addArgument('working-dir', $sig::TYPE_TEXT, 'Cesta k pracovnímu adresáři');
		$sig->addArgument('config', $sig::TYPE_TEXT, 'Jméno configu.');
		$sig->addFlag('show|s', 'Zobrazit volitelně.');
		$sig->addPositional('name', $sig::TYPE_TEXT, 'poziční');
		$sig->addPositional('surname', $sig::TYPE_TEXT, 'poziční');
		$sig->addOptional('size', 42, $sig::TYPE_INT, 'Use age');

		$this->assertEquals(array('working-dir', 'config', 'show', 'name', 'surname', 'size'), $sig->getOptionNames());
		$this->assertNull($sig->getOption('name'));
		$this->assertNull($sig->getOption('surname'));
		$this->assertEqualsOption('show', $sig::TYPE_BOOL, $sig->getOption('show'));
		$this->assertEqualsOption('name', $sig::TYPE_TEXT, $sig->getOptionAt(0));
		$this->assertEqualsOption('surname', $sig::TYPE_TEXT, $sig->getOptionAt(1));
		$this->assertEqualsOption('size', $sig::TYPE_INT, $sig->getOption('size'));
		$this->assertTrue($sig->hasPositional());
	}



	private function assertEqualsOption($name, $type, $item)
	{
		$this->assertEquals($name, $item->getName());
		$this->assertEquals($type, $item->getType());
	}

}
