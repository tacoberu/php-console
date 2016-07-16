<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


require_once __dir__ . '/../../../../vendor/autoload.php';
require_once __dir__ . '/../../../../libs/Taco/Console/interfaces.php';
require_once __dir__ . '/../../../../libs/Taco/Console/Output/HumanOutput.php';
require_once __dir__ . '/../../../../libs/Taco/Console/Output/DictData.php';
require_once __dir__ . '/../../../../libs/Taco/Console/Output/DictDataHumanFormat.php';


use PHPUnit_Framework_TestCase;



/**
 * @call phpunit DictDataHumanFormatTest.php
 */
class DictDataHumanFormatTest extends PHPUnit_Framework_TestCase
{
	private $formater;


	function setUp()
	{
		$stub = $this->getMockBuilder('Taco\Console\Stream')
				->getMock();
		$this->formater = new DictDataHumanFormat(new HumanOutput($stub));
	}


	function testNoticeNull()
	{
		$x = DictData::create('group name');
		$this->assertEquals('', $this->formater->format('notice', $x));
	}


	function testNoticeSample()
	{
		$x = DictData::create('group name')
			->add('first', 'Lorem ipsum')
			->add('second', 'Doler ist.')
			->add('a', 'Extra short label of row.');
		$this->assertEquals('group name
  first   Lorem ipsum
  second  Doler ist.
  a       Extra short label of row.
', $this->formater->format('notice', $x));
	}


	function testNoticeSampleWithoutGroupName()
	{
		$x = DictData::create(null)
			->add('first', 'Lorem ipsum')
			->add('second', 'Doler ist.')
			->add('a', 'Extra short label of row.');
		$this->assertEquals('  first   Lorem ipsum
  second  Doler ist.
  a       Extra short label of row.
', $this->formater->format('notice', $x));
	}



	function testNoticeSubGroup()
	{
		$x = DictData::create('group name')
			->add('first', 'Lorem ipsum')
			->add('second', 'Doler ist.', DictData::create(null)->add('a', 'b'))
			->add('third', 'Extra short label of row.');
		$this->assertEquals('group name
  first  Lorem ipsum
  second Doler ist.
    a  b

  third  Extra short label of row.
', $this->formater->format('notice', $x));
	}

}
