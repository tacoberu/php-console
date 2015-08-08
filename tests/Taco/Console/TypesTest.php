<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


require_once __dir__ . '/../../../vendor/autoload.php';
require_once __dir__ . '/../../../libs/Taco/Console/interfaces.php';
require_once __dir__ . '/../../../libs/Taco/Console/Output.php';
require_once __dir__ . '/../../../libs/Taco/Console/Types.php';


use PHPUnit_Framework_TestCase;



/**
 * @call phpunit TypesTest.php
 */
class TypesTest extends PHPUnit_Framework_TestCase
{


	function testString()
	{
		$t = new TypeText();
		$this->assertSame('male', $t->cast('male'));
	}



	function testInt()
	{
		$t = new TypeInt();
		$this->assertSame(5, $t->cast('5'));
	}



	function testIntInvalidFloat()
	{
		$this->setExpectedException('Taco\Console\TypeException', "Unrecognizable type of int: `5.5'.");
		$t = new TypeInt();
		$t->cast('5.5');
	}



	function testIntInvalidString()
	{
		$this->setExpectedException('Taco\Console\TypeException', "Unrecognizable type of int: `male'.");
		$t = new TypeInt();
		$t->cast('male');
	}



	function testFloat()
	{
		$t = new TypeFloat();
		$this->assertSame(5.0, $t->cast('5'));
		$this->assertSame(5.8, $t->cast('5.8'));
	}



	function testFloatInvalid()
	{
		$this->setExpectedException('Taco\Console\TypeException', "Unrecognizable type of float: `male'.");
		$t = new TypeFloat();
		$t->cast('male');
	}



	function testBoolean()
	{
		$t = new TypeBool();
		$this->assertTrue($t->cast('on'));
		$this->assertTrue($t->cast('true'));
		$this->assertTrue($t->cast('y'));
		$this->assertTrue($t->cast('yes'));
		$this->assertTrue($t->cast('1'));
		$this->assertFalse($t->cast('off'));
		$this->assertFalse($t->cast('no'));
		$this->assertFalse($t->cast('n'));
		$this->assertFalse($t->cast('false'));
		$this->assertFalse($t->cast('0'));
	}



	function testBooleanInvalid()
	{
		$this->setExpectedException('Taco\Console\TypeException', "Unrecognizable type of bool: `male'.");
		$t = new TypeBool();
		$t->cast('male');
	}



	function testEnum()
	{
		$t = new TypeEnum(['male', 'female']);
		$this->assertEquals('male', $t->cast('male'));
	}



	function testEnumEmpty()
	{
		$this->setExpectedException('Taco\Console\TypeException', "Unrecognizable type of enum(male,female,alien): empty.");
		$t = new TypeEnum(['male', 'female', 'alien']);
		$t->cast('');
	}



	function testEnumInvalid()
	{
		$this->setExpectedException('Taco\Console\TypeException', "Unrecognizable type of enum(male,female): `alien'.");
		$t = new TypeEnum(['male', 'female']);
		$t->cast('alien');
	}



	function testSet()
	{
		$t = new TypeSet(['male', 'female']);
		$this->assertEquals(['male'], $t->cast('male'));
	}



	function testSetMany()
	{
		$t = new TypeSet(['male', 'female', 'alien']);
		$this->assertEquals(['alien', 'male'], $t->cast('alien,male'));
	}



	function testSetEmpty()
	{
		$this->setExpectedException('Taco\Console\TypeException', "Unrecognizable type of set(male,female,alien): empty.");
		$t = new TypeSet(['male', 'female', 'alien']);
		$t->cast('');
	}



	function testSetInvalid()
	{
		$this->setExpectedException('Taco\Console\TypeException', "Unrecognizable type of set(male,female,alien): `boot'.");
		$t = new TypeSet(['male', 'female', 'alien']);
		$t->cast('boot');
	}


}
