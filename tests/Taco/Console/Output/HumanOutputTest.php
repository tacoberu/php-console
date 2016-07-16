<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


require_once __dir__ . '/../../../../vendor/autoload.php';
require_once __dir__ . '/../../../../libs/Taco/Console/interfaces.php';
require_once __dir__ . '/../../../../libs/Taco/Console/Output/HumanOutput.php';


use PHPUnit_Framework_TestCase;



/**
 * @call phpunit HumanOutputTest.php
 */
class HumanOutputTest extends PHPUnit_Framework_TestCase
{


	function testNoticeNull()
	{
		$this->expectOutputString("Null\n");
		$x = new HumanOutput(new Stream());
		$x->notice(Null);
	}



	function testNoticeBool()
	{
		$this->expectOutputString("True\n");
		$x = new HumanOutput(new Stream());
		$x->notice(True);
	}



	function testNoticeString()
	{
		$this->expectOutputString("Lorem ipsum\n");
		$x = new HumanOutput(new Stream());
		$x->notice('Lorem ipsum');
	}



	function testErrorString()
	{
		$this->expectOutputString("Lorem ipsum\n");
		$x = new HumanOutput(new Stream());
		$x->error('Lorem ipsum');
	}



	function testNoticeInvalid()
	{
		$this->expectOutputString("! invalid output: `DateTime'\n");
		$x = new HumanOutput(new Stream());
		$x->notice(new \DateTime());
	}



	function testErrorInvalid()
	{
		$this->expectOutputString("! invalid output: `DateTime'\n");
		$x = new HumanOutput(new Stream());
		$x->error(new \DateTime());
	}



}
