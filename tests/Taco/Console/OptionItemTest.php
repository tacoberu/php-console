<?php
/**
 * Copyright (c) since 2004 Martin Takáč
 * @author Martin Takáč <martin@takac.name>
 */

namespace Taco\Console;

use PHPUnit_Framework_TestCase;


/**
 * @call phpunit OptionItemTest.php
 */
class OptionItemTest extends PHPUnit_Framework_TestCase
{


	function _testFlag()
	{
		$item = new FlagOptionItem('flg', 'desc');
		$this->assertEquals('flg', $item->getName());
		$this->assertNull($item->getShortname());
		$this->assertEquals('bool', (string)$item->getType());
		$this->assertEquals('desc', $item->getDescription());
		$this->assertTrue($item->hasDefaultValue());
		$this->assertFalse($item->getDefaultValue());
		$this->assertEquals(0, $item->getValence());
	}



	function testOptText()
	{
		$item = new ConstraintOptionItem(new TypeText, 'flg', 'desc');
		$this->assertEquals('flg', $item->getName());
		$this->assertNull($item->getShortname());
		$this->assertEquals('text', $item->getType());
		$this->assertEquals('desc', $item->getDescription());
		$this->assertFalse($item->hasDefaultValue());
		$this->assertNull($item->getDefaultValue());
		$this->assertEquals('lorem ipsum', $item->parse('lorem ipsum'));
		$this->assertEquals(1, $item->getValence());
	}



	function testOptInt()
	{
		$item = new ConstraintOptionItem(new TypeInt, 'flg', 'desc');
		$this->assertEquals('flg', $item->getName());
		$this->assertNull($item->getShortname());
		$this->assertEquals('int', $item->getType());
		$this->assertEquals('desc', $item->getDescription());
		$this->assertFalse($item->hasDefaultValue());
		$this->assertNull($item->getDefaultValue());
		$this->assertSame(54, $item->parse('54'));
		$this->assertEquals(1, $item->getValence());
	}



	function testOptFloat()
	{
		$item = new ConstraintOptionItem(new TypeFloat, 'flg', 'desc');
		$this->assertEquals('flg', $item->getName());
		$this->assertNull($item->getShortname());
		$this->assertEquals('float', $item->getType());
		$this->assertEquals('desc', $item->getDescription());
		$this->assertFalse($item->hasDefaultValue());
		$this->assertNull($item->getDefaultValue());
		$this->assertEquals(1, $item->getValence());
		$this->assertSame(54.0, $item->parse('54'));
		$this->assertSame(54.4, $item->parse('54.4'));
	}



	function testOptBool()
	{
		$item = new ConstraintOptionItem(new TypeBool, 'flg', 'desc');
		$this->assertEquals('flg', $item->getName());
		$this->assertNull($item->getShortname());
		$this->assertEquals('bool', $item->getType());
		$this->assertEquals('desc', $item->getDescription());
		$this->assertFalse($item->hasDefaultValue());
		$this->assertNull($item->getDefaultValue());
		$this->assertEquals(1, $item->getValence());
		$this->assertSame(true, $item->parse('true'));
		$this->assertSame(true, $item->parse('on'));
		$this->assertSame(true, $item->parse('1'));
		$this->assertSame(true, $item->parse('yes'));
		$this->assertSame(true, $item->parse('y'));
		$this->assertSame(false, $item->parse('false'));
		$this->assertSame(false, $item->parse('off'));
		$this->assertSame(false, $item->parse('0'));
		$this->assertSame(false, $item->parse('no'));
		$this->assertSame(false, $item->parse('n'));
	}



	function testOptTypeEnum()
	{
		$item = new ConstraintOptionItem(new TypeEnum(['male', 'female']), 'name', 'desc');
		$this->assertEquals('name', $item->getName());
		$this->assertEquals('desc', $item->getDescription());
		$this->assertNull($item->getShortname());
		$this->assertEquals('male|female', (string)$item->getType());
		$this->assertEquals(1, $item->getValence());
		$this->assertFalse($item->hasDefaultValue());
		$this->assertNull($item->getDefaultValue());
		$this->assertSame('male', $item->parse('male'));
	}



	function testOptIntDefaultValue()
	{
		$item = new ConstraintOptionItem(new TypeInt, 'flg', 'desc');
		$item->setDefaultValue(42);
		$this->assertTrue($item->hasDefaultValue());
		$this->assertEquals(42, $item->getDefaultValue());
	}



	function testOptIntDefaultValueMustCast()
	{
		$item = new ConstraintOptionItem(new TypeInt, 'flg', 'desc');
		$item->setDefaultValue('42');
		$this->assertTrue($item->hasDefaultValue());
		$this->assertEquals(42, $item->getDefaultValue());
	}



	function testOptIntDefaultValueMayBeClosure()
	{
		$item = new ConstraintOptionItem(new TypeInt, 'flg', 'desc');
		$item->setDefaultValue(function() {
			return 42;
		});
		$this->assertTrue($item->hasDefaultValue());
		$this->assertEquals(42, $item->getDefaultValue());
	}


}
