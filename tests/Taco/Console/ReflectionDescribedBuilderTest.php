<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Console;

use PHPUnit_Framework_TestCase;


/**
 * @call phpunit ReflectionDescribedBuilderTest.php
 * @author Martin Takáč <martin@takac.name>
 */
class ReflectionDescribedBuilderTest extends PHPUnit_Framework_TestCase
{

	function testConstruct()
	{
		$m = ReflectionDescribedBuilder::buildCommand(HelloCommandTest::class);
		$this->assertEquals('hello', $m->getMetaInfo()->name);
		$this->assertEquals('Reprezentuje popis závislost a implementaci vstupních bodů. Jeden kommand je jedna akce, ale může být spouštěna ve více módech (dry-run, check a podobně)', $m->getMetaInfo()->description);
		$this->assertEquals(['output' => 'Taco\Console\Output'], $m->getDepends());
		$this->assertEquals(HelloCommandTest::class, $m->getInvoker());
	}

}



/**
 * @name hello
 * Reprezentuje popis závislost a implementaci vstupních bodů. Jeden kommand
 * je jedna akce, ale může být spouštěna ve více módech (dry-run, check a podobně)
 * @argument("text", "name|n", "Your name")
 * @argument("int", "age", "Your age")
 * @optional("text", "title", "Title", "sir")
 * @author Martin Takáč <martin@takac.name>
 */
class HelloCommandTest implements Command
{

	/**
	 * @param Output $output Where show documentation.
	 */
	function __construct(Output $output)
	{
	}



	/**
	 * Provede výkonný kód.
	 */
	function execute(Options $opts)
	{
	}

}
