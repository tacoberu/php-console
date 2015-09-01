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
require_once __dir__ . '/../../../libs/Taco/Console/Options.php';
require_once __dir__ . '/../../../libs/Taco/Console/OptionItem.php';
require_once __dir__ . '/../../../libs/Taco/Console/OptionSignature.php';


use PHPUnit_Framework_TestCase;



/**
 * @call phpunit OptionItemTest.php
 */
class OptionItemTest extends PHPUnit_Framework_TestCase
{


	function testFlag()
	{
		$item = new FlagOptionItem('flg', 'desc');
		$this->assertEquals('flg', $item->getName());
		$this->assertNull($item->getShortname());
		$this->assertEquals('-', $item->getType());
		$this->assertEquals('desc', $item->getDescription());
		$this->assertFalse($item->hasDefaultValue());
		$this->assertNull($item->getDefaultValue());
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
