<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


require_once __dir__ . '/../../../../vendor/autoload.php';
require_once __dir__ . '/../../../../libs/Taco/Console/interfaces.php';
require_once __dir__ . '/../../../../libs/Taco/Console/Output/DictData.php';


use PHPUnit_Framework_TestCase;



/**
 * @call phpunit DictDataTest.php
 */
class DictDataTest extends PHPUnit_Framework_TestCase
{

	function testNoticeNull()
	{
		$x = DictData::create('group name')
			->add('first', 'Lorem ipsum')
			->add('second', 'Doler ist.');
		$this->assertEquals('group name', $x->getGroupName());
		$this->assertEquals([
			['first', 'Lorem ipsum', Null],
			['second', 'Doler ist.', Null],
		], $x->getItems());
	}

}
