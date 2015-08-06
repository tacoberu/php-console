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
		$this->assertEquals('desc', $item->getdescription());
	}



	function testOptText()
	{
		$item = new ConstraintOptionItem(new TypeText, 'flg', 'desc');
		$this->assertEquals('flg', $item->getName());
		$this->assertEquals('desc', $item->getdescription());
		$this->assertEquals('lorem ipsum', $item->parse('lorem ipsum'));
	}



	function testOptInt()
	{
		$item = new ConstraintOptionItem(new TypeInt, 'flg', 'desc');
		$this->assertEquals('flg', $item->getName());
		$this->assertEquals('desc', $item->getdescription());
		$this->assertSame(54, $item->parse('54'));
	}


}
