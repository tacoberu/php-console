<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


require_once __dir__ . '/../../../../vendor/autoload.php';


use PHPUnit_Framework_TestCase;
use Mockista;



/**
 * @call phpunit VersionCommandTest.php
 */
class VersionCommandTest extends PHPUnit_Framework_TestCase
{


	protected function setUp()
	{
		$this->mockista = new Mockista\Registry();
	}



	function testEntryValue()
	{
		$builder = $this->mockista->createBuilder('Taco\Console\Output');
		$builder->notice('v1.2-3');
		$output = $builder->getMock();
		$cmd = new VersionCommand($output, Version::fromString('1.2.3'));
		// @TODO Tak tyto parametry by mělo odmítnout, jako nevalidní.
		$this->assertEquals(0, $cmd->execute(new Options(['name' => 'Martin', 'age' => '42'])));
	}

}
