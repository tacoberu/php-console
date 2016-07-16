<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


require_once __dir__ . '/../../../../vendor/autoload.php';
require_once __dir__ . '/../../../../libs/Taco/Console/interfaces.php';
require_once __dir__ . '/../../../../libs/Taco/Console/Output/Colors.php';


use PHPUnit_Framework_TestCase;



/**
 * @call phpunit ColorsTest.php
 */
class ColorsTest extends PHPUnit_Framework_TestCase
{

	private $colors;


	function setUp()
	{
		$this->colors = new Colors();
	}



	function testNoop()
	{
		$this->assertSame('text', $this->colors->apply('text'));
	}



	function testNoticeNull()
	{
		$this->assertSame("\033[30;40mblack\033[39;49m", $this->colors->apply('black', 'black', 'black'));
		$this->assertSame("\033[31;41mtext\033[39;49m", $this->colors->apply('text', 'red', 'red'));
		$this->assertSame("\033[32;42mtext\033[39;49m", $this->colors->apply('text', 'green', 'green'));
		$this->assertSame("\033[33;43mtext\033[39;49m", $this->colors->apply('text', 'yellow', 'yellow'));
		$this->assertSame("\033[34;44mtext\033[39;49m", $this->colors->apply('text', 'blue', 'blue'));
		$this->assertSame("\033[35;45mtext\033[39;49m", $this->colors->apply('text', 'magenta', 'magenta'));
		$this->assertSame("\033[36;46mtext\033[39;49m", $this->colors->apply('text', 'cyan', 'cyan'));
		$this->assertSame("\033[37;47mtext\033[39;49m", $this->colors->apply('text', 'white', 'white'));
		//~ $this->assertSame("\033[38;48mtext\033[39;49m", $this->colors->apply('text', '?', '?'));
		$this->assertSame("\033[39;49mtext\033[39;49m", $this->colors->apply('text', 'default', 'default'));
	}



	function testInvalidColor()
	{
		$this->setExpectedException('InvalidArgumentException', "Unsuported foreground color: `no'.");
		$this->colors->apply('text', 'no');
	}


}
