<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


require_once __dir__ . '/../../../../vendor/autoload.php';
require_once __dir__ . '/../../../../libs/Taco/Console/interfaces.php';
require_once __dir__ . '/../../../../libs/Taco/Console/Output/ListData.php';


use PHPUnit_Framework_TestCase;



/**
 * @call phpunit ListFormaterTest.php
 */
class ListDataTest extends PHPUnit_Framework_TestCase
{

	function testNoticeNull()
	{
		$x = ListData::create('group name')
			->add('Lorem ipsum')
			->add('Doler ist.');
		$this->assertEquals('group name', $x->getGroupName());
		$this->assertEquals([
			'Lorem ipsum',
			'Doler ist.',
		], $x->getItems());
	}

}
