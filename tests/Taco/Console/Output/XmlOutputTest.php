<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


require_once __dir__ . '/../../../../vendor/autoload.php';
require_once __dir__ . '/../../../../libs/Taco/Console/interfaces.php';
require_once __dir__ . '/../../../../libs/Taco/Console/Output/XmlOutput.php';


use PHPUnit_Framework_TestCase;



/**
 * @call phpunit XmlOutputTest.php
 */
class XmlOutputTest extends PHPUnit_Framework_TestCase
{

	function testNoticeNull()
	{
		$this->expectOutputString("<output>
	<notice type=\"Null\" />
</output>\n");
		$x = new XmlOutput(new Stream());
		$x->notice(Null);
	}



	function testNoticeBool()
	{
		$this->expectOutputString("<output>
	<notice type=\"False\" />
</output>\n");
		$x = new XmlOutput(new Stream());
		$x->notice(False);
	}



	function testNoticeString()
	{
		$this->expectOutputString("<output>
	<notice>Lorem ipsum</notice>
</output>\n");
		$x = new XmlOutput(new Stream());
		$x->notice('Lorem ipsum');
	}



	function testErrorString()
	{
		$this->expectOutputString("<output>
	<error>Lorem ipsum</error>
</output>\n");
		$x = new XmlOutput(new Stream());
		$x->error('Lorem ipsum');
	}



	function testNoticeInvalid()
	{
		$this->expectOutputString("<output>
	<notice>! invalid output: `DateTime'</notice>
</output>\n");
		$x = new XmlOutput(new Stream());
		$x->notice(new \DateTime());
	}



	function testErrorInvalid()
	{
		$this->expectOutputString("<output>
	<error>! invalid output: `DateTime'</error>
</output>\n");
		$x = new XmlOutput(new Stream());
		$x->error(new \DateTime());
	}



}
