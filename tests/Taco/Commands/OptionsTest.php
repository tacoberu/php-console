<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Commands;


require_once __dir__ . '/../../../vendor/autoload.php';
require_once __dir__ . '/../../../libs/Taco/Commands/interfaces.php';
require_once __dir__ . '/../../../libs/Taco/Commands/Output.php';
require_once __dir__ . '/../../../libs/Taco/Commands/Types.php';
require_once __dir__ . '/../../../libs/Taco/Commands/Options.php';
require_once __dir__ . '/../../../libs/Taco/Commands/OptionItem.php';
require_once __dir__ . '/../../../libs/Taco/Commands/OptionSignature.php';
require_once __dir__ . '/../../../libs/Taco/Commands/HelloCommand.php';


use PHPUnit_Framework_TestCase;



/**
 * @call phpunit OptionsTest.php
 */
class OptionsTest extends PHPUnit_Framework_TestCase
{


	function testFromArray()
	{
		$sign = new OptionSignature();
		$sign->addArgument('name', $sign::TYPE_TEXT, 'Jméno koho pozdravím.');
		$sign->addArgument('age', $sign::TYPE_INT, 'Věk koho pozdravím.');
		$sign->addArgument('flt', $sign::TYPE_FLOAT, 'Číslo s desetinou tečkou.');
		$sign->addOption('title', 'sir', $sign::TYPE_TEXT, 'Má titul?');
		$sign->addArgument('sex', $sign::TYPE_ENUM('male', 'female'), 'Muž či žena?');

		$args = array(
				'name', 'Martin',
				'age', '42',
				'flt', '4.2',
				'title', 'pan',
				'sex', 'male',
				);
		$options = Options::fromArray($args, $sign);
		$this->assertSame('Martin', $options->getOption('name'));
		$this->assertSame(42, $options->getOption('age'));
		$this->assertSame(4.2, $options->getOption('flt'));
		$this->assertSame('pan', $options->getOption('title'));
		$this->assertSame('male', $options->getOption('sex'));
	}



	function testFromArrayWithMark()
	{
		$sign = new OptionSignature();
		$sign->addArgument('name', $sign::TYPE_TEXT, 'Jméno koho pozdravím.');
		$sign->addArgument('age', $sign::TYPE_INT, 'Věk koho pozdravím.');
		$sign->addArgument('flt', $sign::TYPE_FLOAT, 'Číslo s desetinou tečkou.');
		$sign->addOption('title', 'sir', $sign::TYPE_TEXT, 'Má titul?');
		$sign->addArgument('sex', $sign::TYPE_ENUM('male', 'female'), 'Muž či žena?');

		$args = array(
				'name', 'Martin',
				'age', '42',
				'flt', '4.2',
				'--title', 'pan',
				'sex', 'male',
				);
		$options = Options::fromArray($args, $sign);
		$this->assertSame('Martin', $options->getOption('name'));
		$this->assertSame(42, $options->getOption('age'));
		$this->assertSame(4.2, $options->getOption('flt'));
		$this->assertSame('pan', $options->getOption('title'));
		$this->assertSame('male', $options->getOption('sex'));
	}



	function testFromArrayFailBecauseUnknowOption()
	{
		$this->setExpectedException('InvalidArgumentException', "Option `age' not found.");

		$sign = new OptionSignature();
		$sign->addArgument('name', $sign::TYPE_TEXT, 'Jméno koho pozdravím.');

		$args = array(
				'name', 'Martin',
				'age', '42',
				'title', 'pan',
				);
		Options::fromArray($args, $sign);
	}



	function testFromArrayFailBecauseUnusedRequireOptions()
	{
		$this->setExpectedException('InvalidArgumentException', "Option(s) `name' are required.");

		$sign = new OptionSignature();
		$sign->addArgument('name', $sign::TYPE_TEXT, 'Jméno koho pozdravím.');
		Options::fromArray(array(), $sign);
	}

}
