<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


require_once __dir__ . '/../../../../vendor/autoload.php';
require_once __dir__ . '/../../../../libs/Taco/Console/interfaces.php';
require_once __dir__ . '/../../../../libs/Taco/Console/Output/TableData.php';


use PHPUnit_Framework_TestCase;



/**
 * @call phpunit TableDataTest.php
 */
class TableDataTest extends PHPUnit_Framework_TestCase
{

	function testNoticeNull()
	{
		$x = TableData::create(['ISBN', 'Title', 'Author'])
			->addRow(['99921-58-10-7', 'Divine Comedy', 'Dante Alighieri'])
			->addRow(['9971-5-0210-0', 'A Tale of Two Cities', 'Charles Dickens'])
			->addRow(['960-425-059-0', 'The Lord of the Rings', 'J. R. R. Tolkien']);

		$this->assertEquals(['ISBN', 'Title', 'Author'], $x->getHeaders());
		$this->assertEquals([
			['99921-58-10-7', 'Divine Comedy', 'Dante Alighieri'],
			['9971-5-0210-0', 'A Tale of Two Cities', 'Charles Dickens'],
			['960-425-059-0', 'The Lord of the Rings', 'J. R. R. Tolkien'],
		], $x->getItems());
	}

}
