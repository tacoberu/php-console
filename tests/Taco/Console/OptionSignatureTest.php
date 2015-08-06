<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


require_once __dir__ . '/../../../vendor/autoload.php';


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
 * Jsou dvě skupiny parametrů:
 * - argumenty: které jsou poziční, ale pomocí jména je možné je vyjmout z pořadí.
 * - optiony:   které jsou mimo pozicy.
 *
 * @call phpunit OptionSignatureTest.php
 */
class OptionSignatureTest extends PHPUnit_Framework_TestCase
{


	/**
	 * Vyžadované parametry jsou poziční. Mohou se zadat bez uvedení jména, a nebo s uvedením jména a tím pořadí přeskočit.
	 */
	function testRequiredArgument1()
	{
		$sig = new OptionSignature();
		$sig->addArgument('name|n', $sig::TYPE_TEXT, '...');
		$sig->addArgumentDefault('age|a', $sig::TYPE_INT, 42, '...');
		$sig->addOption('sex', $sig::TYPE_ENUM(['male','female']), 'male', '...');

		$this->assertEquals(array('name', 'age', 'sex'), $sig->getOptionNames(), 'List of option names.');

		$this->assertEqualsOption('name', $sig::TYPE_TEXT, $sig->getOptionAt(0));
		$this->assertEqualsOption('age', $sig::TYPE_INT, $sig->getOptionAt(1));

		$this->assertEquals(array('age' => 42, 'sex' => 'male'), $sig->getDefaultValues(), 'List of option names.');
	}



	/**
	 * Vyžadované parametry jsou poziční. Mohou se zadat bez uvedení jména, a nebo s uvedením jména a tím pořadí přeskočit.
	 */
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
	}



	/**
	 * Volitelné parametry musí být vždy uvedeny jménem.
	 */
	function testOptionalArgument()
	{
		$sig = new OptionSignature();
		//~ $sig->addOptional('show|s', True, $sig::TYPE_BOOL, 'Zobrazit volitelně.');
		$sig->addOption('show|s', $sig::TYPE_BOOL, True, 'Zobrazit volitelně.');
		$sig->addOption('size', $sig::TYPE_INT, 42, 'Use age');

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



	function testComplex()
	{
		$sig = new OptionSignature();
		$sig->addOption('working-dir', $sig::TYPE_TEXT, '.', 'Cesta k pracovnímu adresáři');
		$sig->addOption('config', $sig::TYPE_TEXT, 'config.ini', 'Jméno configu.');
		$sig->addFlag('show|s', 'Zobrazit volitelně.');
		$sig->addArgument('name', $sig::TYPE_TEXT, 'poziční');
		$sig->addArgument('surname', $sig::TYPE_TEXT, 'poziční');
		$sig->addArgumentDefault('size', $sig::TYPE_INT, 42, 'Use age');

		$this->assertEquals(array('name', 'surname', 'size', 'working-dir', 'config', 'show'), $sig->getOptionNames());
		$this->assertEqualsOption('show', $sig::TYPE_BOOL, $sig->getOption('show'));
		$this->assertEqualsOption('name', $sig::TYPE_TEXT, $sig->getOptionAt(0));
		$this->assertEqualsOption('surname', $sig::TYPE_TEXT, $sig->getOptionAt(1));
		$this->assertEqualsOption('size', $sig::TYPE_INT, $sig->getOption('size'));
	}



	function testMergeSignature()
	{
		$a = new OptionSignature();
		$a->addArgument('a1', $a::TYPE_TEXT, '..');
		$a->addArgumentDefault('a2', $a::TYPE_INT, 42, '..');
		$a->addOption('a3', $a::TYPE_TEXT, 'config.ini', '...');

		$b = new OptionSignature();
		$b->addArgument('b1', $b::TYPE_TEXT, '..');
		$b->addArgumentDefault('b2|x', $b::TYPE_INT, 42, '..');
		$b->addOption('b3|y', $b::TYPE_TEXT, 'config.ini', '...');

		$a->merge($b);
		$this->assertEquals(array('a1', 'a2', 'b1', 'b2', 'a3', 'b3'), $a->getOptionNames());
		$this->assertEqualsOption('a1', $a::TYPE_TEXT, $a->getOptionAt(0));
		$this->assertEqualsOption('a2', $a::TYPE_INT, $a->getOptionAt(1));
		$this->assertEqualsOption('b1', $a::TYPE_TEXT, $a->getOptionAt(2));
		$this->assertEqualsOption('b2', $a::TYPE_INT, $a->getOptionAt(3));
		$this->assertEquals('x', $a->getOptionAt(3)->getShortname());
		$this->assertEquals('y', $a->getOption('b3')->getShortname());
	}



	private function assertEqualsOption($name, $type, $item)
	{
		$this->assertEquals($name, $item->getName());
		$this->assertEquals($type, $item->getType());
	}

}
