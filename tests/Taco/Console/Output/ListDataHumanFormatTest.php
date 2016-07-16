<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


require_once __dir__ . '/../../../../vendor/autoload.php';
require_once __dir__ . '/../../../../libs/Taco/Console/interfaces.php';
require_once __dir__ . '/../../../../libs/Taco/Console/Output/HumanOutput.php';
require_once __dir__ . '/../../../../libs/Taco/Console/Output/ListData.php';
require_once __dir__ . '/../../../../libs/Taco/Console/Output/ListDataHumanFormat.php';


use PHPUnit_Framework_TestCase;



/**
 * @call phpunit ListDataHumanFormatTest.php
 */
class ListDataHumanFormatTest extends PHPUnit_Framework_TestCase
{
	private $formater;


	function setUp()
	{
		$stub = $this->getMockBuilder('Taco\Console\Stream')
				->getMock();
		$this->formater = new ListDataHumanFormat(new HumanOutput($stub));
	}


	function testNoticeNull()
	{
		$x = ListData::create('group name')
			->add('Lorem ipsum')
			->add('Doler ist.');
		$this->assertEquals('group name
 - Lorem ipsum
 - Doler ist.
', $this->formater->format('notice', $x));
	}

}
