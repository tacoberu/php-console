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
require_once __dir__ . '/../../../../libs/Taco/Console/Output/TableData.php';
require_once __dir__ . '/../../../../libs/Taco/Console/Output/TableDataXmlFormat.php';


use PHPUnit_Framework_TestCase;



/**
 * @call phpunit TableDataHumanFormatTest.php
 */
class TableDataXmlFormatTest extends PHPUnit_Framework_TestCase
{

	private $formater;


	function setUp()
	{
		$stub = $this->getMockBuilder('Taco\Console\Stream')
				->getMock();
		$this->formater = new TableDataXmlFormat(new XmlOutput($stub));
	}


	function testNoticeNull()
	{
		$x = TableData::create(['ISBN', 'Title', 'Author'])
			->addRow(['99921-58-10-7', 'Divine Comedy', 'Dante Alighieri'])
			->addRow(['9971-5-0210-0', 'A Tale of Two Cities', 'Charles Dickens'])
			->addRow(['960-425-059-0', 'The Lord of the Rings', 'J. R. R. Tolkien']);
		$this->assertEquals('<table>
	<tr>
		<th>ISBN</th>
		<th>Title</th>
		<th>Author</th>
	</tr>
	<tr>
		<td>99921-58-10-7</td>
		<td>Divine Comedy</td>
		<td>Dante Alighieri</td>
	</tr>
	<tr>
		<td>9971-5-0210-0</td>
		<td>A Tale of Two Cities</td>
		<td>Charles Dickens</td>
	</tr>
	<tr>
		<td>960-425-059-0</td>
		<td>The Lord of the Rings</td>
		<td>J. R. R. Tolkien</td>
	</tr>
</table>
', $this->formater->format('notice', $x));
	}

}
