<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;

use PHPUnit_Framework_TestCase;


/**
 * @call phpunit AppInfoTest.php
 */
class AppInfoTest extends PHPUnit_Framework_TestCase
{


	function testConstruct()
	{
		$m = new AppInfo('name', 'desc', 'epilog');
		$this->assertState('name', 'desc', 'epilog', $m);
	}



	function testConstructWithoutEpilog()
	{
		$m = new AppInfo('name', 'desc');
		$this->assertState('name', 'desc', Null, $m);
	}



	private function assertState($name, $description, $epilog, $m)
	{
		$this->assertSame($name, $m->getName());
		$this->assertSame($description, $m->getDescription());
		$this->assertSame($epilog, $m->getEpilog());
	}

}
