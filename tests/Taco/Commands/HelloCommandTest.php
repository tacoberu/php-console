<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Commands;


require_once __dir__ . '/../../../vendor/autoload.php';
require_once __dir__ . '/../../../libs/Taco/Commands/interfaces.php';
require_once __dir__ . '/../../../libs/Taco/Commands/Output.php';
require_once __dir__ . '/../../../libs/Taco/Commands/Options.php';
require_once __dir__ . '/../../../libs/Taco/Commands/OptionItem.php';
require_once __dir__ . '/../../../libs/Taco/Commands/OptionSignature.php';
require_once __dir__ . '/../../../libs/Taco/Commands/HelloCommand.php';


use PHPUnit_Framework_TestCase;



/**
 * @call phpunit HelloCommandTest.php
 */
class HelloCommandTest extends PHPUnit_Framework_TestCase
{


	function testEntryValue()
	{
		$output = new Output();
		$cmd = new HelloCommand($output);
		$this->assertEquals('hello', $cmd->getName());
		$this->assertEquals('Toto je ukázkový command.', $cmd->getDescription());

		$options = Options::fromArray(array('name', 'Martin', 'age', '42'), $cmd->getOptionSignature());

		//~ print_r($options);
		$cmd->execute($options);
	}

}
