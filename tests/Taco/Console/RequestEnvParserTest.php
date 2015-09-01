<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


require_once __dir__ . '/../../../vendor/autoload.php';


use PHPUnit_Framework_TestCase;



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
		$this->setExpectedException('InvalidArgumentException', "Chybí proměnná prostředí \$argv.");
		$this->parser->parse([]);
	}



	function testEmpty()
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
		//~ $this->assertEquals($req->getOptions());
	}



	function testNamed()
	{
		$req = $this->parser->parse([
			'argv' => [ "samples/a/src/app", "hello", "-a", "45", "Martin", ],
			'argc' => 5,
			'_SERVER' => [
				'PWD' => '/home/foo/projects',
			],
		]);
		$this->assertTrue($req->isMissingRules(), 'Přebívají nám hodnoty, které nemáme kam zařadit.');
		$this->assertTrue($req->isFilled());
		$this->assertEquals(['command' => 'hello'], $req->getOptions()->asArray());

		$sign = new OptionSignature();
		$sign->addArgument('name|n', $sign::TYPE_TEXT, 'Jméno koho pozdravím.');
		$sign->addArgument('age|a', $sign::TYPE_INT, 'Věk koho pozdravím.');
		$sign->addArgumentDefault('title', $sign::TYPE_TEXT, 'sir', 'Má titul?');
		$req->applyRules($sign);

		$this->assertFalse($req->isMissingRules(), 'Vše zpracováno.');
		$this->assertEquals([
				'command' => 'hello',
				'title' => 'sir',
				'age' => 45,
				'name' => 'Martin',
				], $req->getOptions()->asArray());
	}



	function testPositionaled()
	{
		$req = $this->parser->parse([
			'argv' => [ "samples/a/src/app", "hello", "Martin", '45'],
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
		$this->assertEquals([
				'command' => 'hello',
				'title' => 'sir',
				'age' => 45,
				'name' => 'Martin',
				], $req->getOptions()->asArray());
	}



	function _testMultiple()
	{
		$req = $this->parser->parse([
			'argv' => [ "samples/a/src/app", "hello", "Martin", '-a', '45', '-a', '55'],
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
		//~ dump($req->getOptions()->asArray());
		$this->assertEquals([
				'command' => 'hello',
				'title' => 'sir',
				'age' => [45, 55,],
				'name' => 'Martin',
				], $req->getOptions()->asArray());
	}



	function testNamedWithEq()
	{
		$req = $this->parser->parse([
			'argv' => [ "samples/a/src/app", "hello", "Martin", '-a=45', ],
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
		//~ dump($req->getOptions()->asArray());
		$this->assertEquals([
				'command' => 'hello',
				'title' => 'sir',
				'age' => 45,
				'name' => 'Martin',
				], $req->getOptions()->asArray());
	}


}
