<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


require_once __dir__ . '/../../../vendor/autoload.php';


use PHPUnit_Framework_TestCase;



/**
 * @call phpunit AuthorTest.php
 */
class AuthorTest extends PHPUnit_Framework_TestCase
{


	function testConstruct()
	{
		$m = new Author('name');
		$this->assertState('name', null, $m);
		$this->assertSame('name', (string)$m);
	}



	function testConstruct2()
	{
		$m = new Author('name', 'm@b.cd');
		$this->assertState('name', 'm@b.cd', $m);
		$this->assertSame('name <m@b.cd>', (string)$m);
	}



	function testParseOne()
	{
		$this->assertState('abc', Null, Author::fromString('abc'));
		$this->assertState('Name Surname', Null, Author::fromString('Name Surname'));
		$this->assertState('Name Surname', Null, Author::fromString('Name Surname '));
		$this->assertState('Name Surname', 'name@surname.dom', Author::fromString('Name Surname <name@surname.dom>'));
		$this->assertState('Name Surname', 'name@surname.dom', Author::fromString(' Name Surname <name@surname.dom> '));
		//~ $this->fail(Author::fromString('<name@surname.dom> '));
	}



	private function assertState($name, $email, $m)
	{
		$this->assertSame($name, $m->getName());
		$this->assertSame($email, $m->getEmail());
	}

}
