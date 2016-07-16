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
require_once __dir__ . '/../../../../libs/Taco/Console/Output/DictData.php';
require_once __dir__ . '/../../../../libs/Taco/Console/Output/DictDataXmlFormat.php';


use PHPUnit_Framework_TestCase;



/**
 * @call phpunit DictDataXmlFormatTest.php
 */
class DictDataXmlFormatTest extends PHPUnit_Framework_TestCase
{

	private $formater;


	function setUp()
	{
		$stub = $this->getMockBuilder('Taco\Console\Stream')
				->getMock();
		$this->formater = new DictDataXmlFormat(new XmlOutput($stub));
	}



	function testNoticeNull()
	{
		$x = DictData::create('group name')
			->add('first', 'Lorem ipsum')
			->add('second', 'Doler ist.');
		$this->assertEquals('<dict label="group name">
	<item label="first">Lorem ipsum</item>
	<item label="second">Doler ist.</item>
</dict>
', $this->formater->format('notice', $x));
	}



	function testWithoutGroupName()
	{
		$x = DictData::create(Null)
			->add('first', 'Lorem ipsum')
			->add('second', 'Doler ist.');
		$this->assertEquals('<dict>
	<item label="first">Lorem ipsum</item>
	<item label="second">Doler ist.</item>
</dict>
', $this->formater->format('notice', $x));
	}



	function testSubGroup()
	{
		$x = DictData::create('group name')
			->add('first', 'Lorem ipsum', DictData::create(Null)
					->add('aa', 1)
					->add('bb', 42)
					)
			->add('second', 'Doler ist.');
		$this->assertEquals('<dict label="group name">
	<item label="first">Lorem ipsum
		<dict>
			<item label="aa">1</item>
			<item label="bb">42</item>
		</dict>
	</item>
	<item label="second">Doler ist.</item>
</dict>
', $this->formater->format('notice', $x));
	}

}
