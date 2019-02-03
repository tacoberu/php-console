<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


require_once __dir__ . '/../../../vendor/autoload.php';


use PHPUnit_Framework_TestCase;



/**
 * @call phpunit VersionTest.php
 */
class VersionTest extends PHPUnit_Framework_TestCase
{


	function testConstruct()
	{
		$m = new Version(1, 2, 3);
		$this->assertState(1, 2, 3, $m);
		$this->assertSame('1.2-3', (string)$m);
	}



	function testParseOne()
	{
		$m = Version::fromString('1.2-3');
		$this->assertState(1, 2, 3, $m);
		$this->assertSame('1.2-3', (string)$m);
	}



	function testParseSecond()
	{
		$m = Version::fromString('1.2.3');
		$this->assertState(1, 2, 3, $m);
		$this->assertSame('1.2-3', (string)$m);
	}



	private function assertState($major, $minor, $release, $m)
	{
		$this->assertSame($major, $m->getMajor());
		$this->assertSame($minor, $m->getMinor());
		$this->assertSame($release, $m->getRelease());
	}

}
