<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


require_once __dir__ . '/../../../../vendor/autoload.php';
require_once __dir__ . '/../../../../libs/Taco/Console/interfaces.php';
require_once __dir__ . '/../../../../libs/Taco/Console/Output/Stream.php';
require_once __dir__ . '/../../../../libs/Taco/Console/Output/XmlOutput.php';
require_once __dir__ . '/../../../../libs/Taco/Console/Output/ListData.php';
require_once __dir__ . '/../../../../libs/Taco/Console/Output/ListDataXmlFormat.php';


use PHPUnit_Framework_TestCase;



/**
 * @call phpunit ListDataHumanFormatTest.php
 */
class ListDataXmlFormatTest extends PHPUnit_Framework_TestCase
{
	private $formater;


	function setUp()
	{
		$stub = $this->getMockBuilder('Taco\Console\Stream')
				->getMock();
		$this->formater = new ListDataXmlFormat(new XmlOutput($stub));
	}


	function testNoticeNull()
	{
		$x = ListData::create('group name')
			->add('Lorem ipsum')
			->add('Doler ist.');
		$this->assertEquals('<list label="group name">
	<item>Lorem ipsum</item>
	<item>Doler ist.</item>
</list>
', $this->formater->format('notice', $x));
	}

}
