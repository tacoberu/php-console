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

	private $parser;


	function setUp()
	{
		$this->parser = new RequestEnvParser();
	}



	function testEmptyFail()
	{
		$this->setExpectedException(InvalidArgumentException::class, "Missing `\$argv' environment variable.");
		$this->parser->parse([]);
	}



	function testEmptyWithoutRequiredCommand()
	{
		$req = $this->parser->parse([
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
		$this->parser = new RequestEnvParser('help');
		$req = $this->parser->parse([
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
		], $req);
	}



	function testEmptyMissingArg()
	{
		$req = $this->parser->parse([
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
  --command  [text]  command
  --name, -n  [text]  Jméno koho pozdravím.");
		$req = $this->parser->parse([
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



	function testNamed()
	{
		$req = $this->parser->parse([
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
		], $req);
	}



	function testPositionaled()
	{
		$req = $this->parser->parse([
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
		], $req);
	}



	function testMultiple()
	{
		$req = $this->parser->parse([
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
		], $req);
	}



	function testNamedWithEq()
	{
		$req = $this->parser->parse([
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
		], $req);
	}



	function _testNamedWithColon()
	{
		$req = $this->parser->parse([
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



	private function assertOptions(array $exept, Request $req)
	{
		$this->assertEquals($exept, $req->getOptions()->asArray());
	}

}
