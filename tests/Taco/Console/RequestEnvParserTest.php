<?php
/**
 * Copyright (c) since 2004 Martin Takáč
 * @author Martin Takáč <martin@takac.name>
 */

namespace Taco\Console;

use PHPUnit_Framework_TestCase;
use InvalidArgumentException;
use RuntimeException;


/**
 * @call phpunit RequestEnvParserTest.php
 */
class RequestEnvParserTest extends PHPUnit_Framework_TestCase
{


	function testEmptyFail()
	{
		$this->setExpectedException(InvalidArgumentException::class, "Missing `\$argv' environment variable.");
		$this->getEmptyParser()->parse([]);
	}



	function testEmpty()
	{
		$req = $this->getEmptyParser()->parse([
			'argv' => [],
			'argc' => 0,
			'_SERVER' => [
				'PWD' => '/home/foo/projects',
			],
		]);
		$this->assertFalse($req->isMissingRules());
		$this->assertTrue($req->isFilled());
	}



	function testEmptyWithoutRequiredCommand()
	{
		$req = $this->getCommandParser()->parse([
			'argv' => [],
			'argc' => 0,
			'_SERVER' => [
				'PWD' => '/home/foo/projects',
			],
		]);
		$this->assertFalse($req->isMissingRules());
		$this->assertFalse($req->isFilled());
	}



	function testEmptyWithDefaultCommand()
	{
		$req = $this->getCommandParser('help')->parse([
			'argv' => [],
			'argc' => 0,
			'_SERVER' => [
				'PWD' => '/home/foo/projects',
			],
		]);
		$this->assertFalse($req->isMissingRules());
		$this->assertTrue($req->isFilled());
		$this->assertOptions([
			'command' => 'help',
			'trace' => false,
			'working-dir' => '/home/foo/projects',
		], $req);
	}



	function testEmptyMissingArg()
	{
		$req = $this->getEmptyParser()->parse([
			'argv' => [],
			'argc' => 0,
			'_SERVER' => [
				'PWD' => '/home/foo/projects',
			],
		]);

		$sign = new OptionSignature();
		$sign->addArgument('name|n', $sign::TYPE_TEXT, 'Jméno koho pozdravím.');
		$req->applyRules($sign);

		$this->assertFalse($req->isMissingRules());
		$this->assertFalse($req->isFilled());
	}



	function testEmptyMissingArgException()
	{
		$this->setExpectedException(RuntimeException::class, "Missing required options:
  --name, -n  [text]  Jméno koho pozdravím.");
		$req = $this->getEmptyParser()->parse([
			'argv' => [],
			'argc' => 0,
			'_SERVER' => [
				'PWD' => '/home/foo/projects',
			],
		]);

		$sign = new OptionSignature();
		$sign->addArgument('name|n', $sign::TYPE_TEXT, 'Jméno koho pozdravím.');
		$req->applyRules($sign);

		$this->assertFalse($req->isMissingRules());
		$this->assertFalse($req->isFilled());
		$req->getOptions();
	}



	function testEmptyMissingArgExceptionWithCommand()
	{
		$this->setExpectedException(RuntimeException::class, "Missing required options:
  --command  [text]  The command name
  --name, -n  [text]  Jméno koho pozdravím.");
		$req = $this->getCommandParser()->parse([
			'argv' => [],
			'argc' => 0,
			'_SERVER' => [
				'PWD' => '/home/foo/projects',
			],
		]);

		$sign = new OptionSignature();
		$sign->addArgument('name|n', $sign::TYPE_TEXT, 'Jméno koho pozdravím.');
		$req->applyRules($sign);

		$this->assertFalse($req->isMissingRules());
		$this->assertFalse($req->isFilled());
		$req->getOptions();
	}



	function testCommandedNamed()
	{
		$req = $this->getCommandParser()->parse([
			'argv' => [ "samples/a/src/app", 'run', "-a", "45", "Martin", ],
			'argc' => 5,
			'_SERVER' => [
				'PWD' => '/home/foo/projects',
			],
		]);
		$this->assertTrue($req->isMissingRules(), 'Přebívají nám hodnoty, které nemáme kam zařadit.');
		$this->assertTrue($req->isFilled());
		$this->assertOptions([
			'command' => 'run',
			'trace' => false,
			'working-dir' => '/home/foo/projects',
		], $req);

		$sign = new OptionSignature();
		$sign->addArgument('name|n', $sign::TYPE_TEXT, 'Jméno koho pozdravím.');
		$sign->addArgument('age|a', $sign::TYPE_INT, 'Věk koho pozdravím.');
		$sign->addArgumentDefault('title', $sign::TYPE_TEXT, 'sir', 'Má titul?');
		$req->applyRules($sign);

		$this->assertFalse($req->isMissingRules(), 'Vše zpracováno.');
		$this->assertOptions([
			'command' => 'run',
			'title' => 'sir',
			'age' => 45,
			'name' => 'Martin',
			'trace' => false,
			'working-dir' => '/home/foo/projects',
		], $req);
	}



	function testDefaultWorkingDir()
	{
		$req = $this->getDefaultParser()->parse([
			'argv' => [ "samples/a/src/app", "-a", "45", "Martin", '--working-dir', '/temp', ],
			'argc' => 5,
			'_SERVER' => [
				'PWD' => '/home/foo/projects',
			],
		]);

		$sign = new OptionSignature();
		$sign->addArgument('name|n', $sign::TYPE_TEXT, 'Jméno koho pozdravím.');
		$sign->addArgument('age|a', $sign::TYPE_INT, 'Věk koho pozdravím.');
		$req->applyRules($sign);

		$this->assertFalse($req->isMissingRules(), 'Vše zpracováno.');

		$this->assertOptions([
			'age' => 45,
			'name' => 'Martin',
			'trace' => false,
			'working-dir' => '/temp',
		], $req);
	}



	function testWorkingDir()
	{
		$req = $this->getCommandParser()->parse([
			'argv' => [ "samples/a/src/app", 'run', "-a", "45", "Martin", '--working-dir', '/temp', ],
			'argc' => 5,
			'_SERVER' => [
				'PWD' => '/home/foo/projects',
			],
		]);

		$sign = new OptionSignature();
		$sign->addArgument('name|n', $sign::TYPE_TEXT, 'Jméno koho pozdravím.');
		$sign->addArgument('age|a', $sign::TYPE_INT, 'Věk koho pozdravím.');
		$req->applyRules($sign);

		$this->assertFalse($req->isMissingRules(), 'Vše zpracováno.');

		$this->assertOptions([
			'command' => 'run',
			'age' => 45,
			'name' => 'Martin',
			'trace' => false,
			'working-dir' => '/temp',
		], $req);
	}



	function testWorkingDirShort()
	{
		$req = $this->getCommandParser()->parse([
			'argv' => [ "samples/a/src/app", 'run', "-a", "45", "Martin", '-d', '/temp', ],
			'argc' => 5,
			'_SERVER' => [
				'PWD' => '/home/foo/projects',
			],
		]);

		$sign = new OptionSignature();
		$sign->addArgument('name|n', $sign::TYPE_TEXT, 'Jméno koho pozdravím.');
		$sign->addArgument('age|a', $sign::TYPE_INT, 'Věk koho pozdravím.');
		$req->applyRules($sign);

		$this->assertFalse($req->isMissingRules(), 'Vše zpracováno.');

		$this->assertOptions([
			'command' => 'run',
			'age' => 45,
			'name' => 'Martin',
			'trace' => false,
			'working-dir' => '/temp',
		], $req);
	}



	/**
	 * toto asi nemůže jít. Protože --file mám definováno až díky commandu "run" a já nevím, jaké má argumenty, dokavad nevím jaký je to command.
	 */
	function _testBug1()
	{
		$req = $this->parser->parse([
			'argv' => [ "samples/a/src/app", "--file", "samples/001.helloworld.hockej", "run", ],
			'argc' => 4,
			'_SERVER' => [
				'PWD' => '/home/foo/projects',
			],
		]);
		dump($req);
		$this->assertTrue($req->isMissingRules(), 'Přebívají nám hodnoty, které nemáme kam zařadit.');
		/*
		$this->assertTrue($req->isFilled());
		$this->assertOptions([
			'command' => 'run',
			'trace' => false,
		], $req);

		$sign = new OptionSignature();
		$sign->addArgument('name|n', $sign::TYPE_TEXT, 'Jméno koho pozdravím.');
		$sign->addArgument('age|a', $sign::TYPE_INT, 'Věk koho pozdravím.');
		$sign->addArgumentDefault('title', $sign::TYPE_TEXT, 'sir', 'Má titul?');
		$req->applyRules($sign);

		$this->assertFalse($req->isMissingRules(), 'Vše zpracováno.');
		$this->assertOptions([
			'command' => 'run',
			'title' => 'sir',
			'age' => 45,
			'name' => 'Martin',
			'trace' => false,
		], $req);
		*/
	}



	function testPositionaled()
	{
		$req = $this->getCommandParser()->parse([
			'argv' => [ "samples/a/src/app", 'run', "Martin", '45'],
			'_SERVER' => [
				'PWD' => '/home/foo/projects',
			],
		]);
		$sign = new OptionSignature();
		$sign->addArgument('name|n', $sign::TYPE_TEXT, 'Jméno koho pozdravím.');
		$sign->addArgument('age|a', $sign::TYPE_INT, 'Věk koho pozdravím.');
		$sign->addArgumentDefault('title', $sign::TYPE_TEXT, 'sir', 'Má titul?');
		$req->applyRules($sign);

		$this->assertFalse($req->isMissingRules(), 'Vše zpracováno.');
		$this->assertOptions([
			'command' => 'run',
			'title' => 'sir',
			'age' => 45,
			'name' => 'Martin',
			'trace' => false,
			'working-dir' => '/home/foo/projects',
		], $req);
	}



	function testMultiple()
	{
		$req = $this->getCommandParser()->parse([
			'argv' => [ "samples/a/src/app", 'run', "Martin", '-a', '45', '-a', '55'],
			'_SERVER' => [
				'PWD' => '/home/foo/projects',
			],
		]);
		$sign = new OptionSignature();
		$sign->addArgument('name|n', $sign::TYPE_TEXT, 'Jméno koho pozdravím.');
		$sign->addArgument('age|a', $sign::TYPE_INT, 'Věk koho pozdravím.');
		$sign->addArgumentDefault('title', $sign::TYPE_TEXT, 'sir', 'Má titul?');
		$req->applyRules($sign);

		$this->assertFalse($req->isMissingRules(), 'Vše zpracováno.');
		$this->assertOptions([
			'command' => 'run',
			'title' => 'sir',
			'age' => 55, // [45, 55,],
			'name' => 'Martin',
			'trace' => false,
			'working-dir' => '/home/foo/projects',
		], $req);
	}



	function testNamedWithEq()
	{
		$req = $this->getCommandParser()->parse([
			'argv' => [ "samples/a/src/app", 'run', "Martin", '-a=45', ],
			'_SERVER' => [
				'PWD' => '/home/foo/projects',
			],
		]);
		$sign = new OptionSignature();
		$sign->addArgument('name|n', $sign::TYPE_TEXT, 'Jméno koho pozdravím.');
		$sign->addArgument('age|a', $sign::TYPE_INT, 'Věk koho pozdravím.');
		$sign->addArgumentDefault('title', $sign::TYPE_TEXT, 'sir', 'Má titul?');
		$req->applyRules($sign);

		$this->assertFalse($req->isMissingRules(), 'Vše zpracováno.');
		$this->assertOptions([
			'command' => 'run',
			'title' => 'sir',
			'age' => 45,
			'name' => 'Martin',
			'trace' => false,
			'working-dir' => '/home/foo/projects',
		], $req);
	}



	function _testNamedWithColon()
	{
		$req = $this->getCommandParser()->parse([
			'argv' => [ "samples/a/src/app", "Martin", '-a:45', ],
			'_SERVER' => [
				'PWD' => '/home/foo/projects',
			],
		]);
		$sign = new OptionSignature();
		$sign->addArgument('name|n', $sign::TYPE_TEXT, 'Jméno koho pozdravím.');
		$sign->addArgument('age|a', $sign::TYPE_INT, 'Věk koho pozdravím.');
		$sign->addArgumentDefault('title', $sign::TYPE_TEXT, 'sir', 'Má titul?');
		$req->applyRules($sign);

		$this->assertFalse($req->isMissingRules(), 'Vše zpracováno.');
		$this->assertOptions([
			'title' => 'sir',
			'age' => 45,
			'name' => 'Martin',
		], $req);
	}



	private function getEmptyParser()
	{
		return new RequestEnvParser();
	}



	private function getCommandParser($default = Null)
	{
		return RequestEnvParser::createCommanded($default);
	}



	private function getDefaultParser()
	{
		return RequestEnvParser::createDefault();
	}



	private function assertOptions(array $exept, Request $req)
	{
		$this->assertEquals($exept, $req->getOptions()->asArray());
	}

}
