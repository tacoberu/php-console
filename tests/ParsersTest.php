<?php
/**
 * Copyright (c) since 2004 Martin Takáč
 * @author Martin Takáč <martin@takac.name>
 */

namespace Taco\Console;

use PHPUnit_Framework_TestCase;


class ParsersTest extends PHPUnit_Framework_TestCase
{


	/**
	 * @dataProvider dataParseTypeSchema
	 */
	function testParseTypeSchema($value, $expected)
	{
		$this->assertEquals($expected, Parsers::parseTypeSchema($value));
	}



	function dataParseTypeSchema()
	{
		return [
			[[], []],
			[[
				'abc'
				], [
					['required', 'abc', 'text', '~'],
				]],
			[[
				['abc']
				], [
					['required', 'abc', 'text', '~'],
				]],
			[[
				['abc', 'string']
				], [
					['required', 'abc', 'string', '~'],
				]],
			[[
				['abc', new \DateTime] // že je to DateTime není nijak podstatné. Jde jen o to, že je to objekt.
				], [
					['required', 'abc', new \DateTime, '~'],
				]],
			[[
				['abc', 'string', 'Doc comment.']
				], [
					['required', 'abc', 'string', 'Doc comment.'],
				]],
			[[
				['abc', 'int']
				], [
					['required', 'abc', 'int', '~'],
				]],
			[[
				['?abc', 'default-value']
				], [
					['optional', 'abc', 'text', '~', 'default-value'],
				]],
			[[
				['?abc', 12]
				], [
					['optional', 'abc', 'int', '~', 12],
				]],
			[[
				['?abc', 1.2]
				], [
					['optional', 'abc', 'float', '~', 1.2],
				]],
			[[
				['?abc', 'default-value', 'int']
				], [
					['optional', 'abc', 'int', '~', 'default-value'],
				]],
			[[
				['?abc', 1.4, null]
				], [
					['optional', 'abc', 'float', '~', 1.4],
				]],
			[[
				['?abc', 1.8, null, 'doc comment']
				], [
					['optional', 'abc', 'float', 'doc comment', 1.8],
				]],
			[[
				['?abc', false]
				], [
					['optional', 'abc', 'bool', '~', false],
				]],
			[[
				['?abc', true]
				], [
					['optional', 'abc', 'bool', '~', true],
				]],
		];
	}



	/**
	 * @dataProvider dataParseSignatureFromDocComment
	 */
	function testParseSignatureFromDocComment($value, $expected)
	{
		$this->assertEquals($expected, Parsers::parseSignatureFromDocComment($value));
	}



	function dataParseSignatureFromDocComment()
	{
		return [
			['', []],
			["/**\n */", []],
			["/**\n * @argument string \$desc doc comment\n */", [
				['required', 'desc', 'string', 'doc comment']
				]],
			["/**\n * @optional string \$desc doc comment\n */", [
				['optional', 'desc', 'string', 'doc comment']
				]],
			["/**\n"
			. " * @argument string \$desc doc comment\n"
			. " *"
			. " */", [
				['required', 'desc', 'string', 'doc comment']
				]],
			["/**\n"
			. " * @argument string \$desc doc comment\n"
			. " * @optional int \$limit doc limit comment\n"
			. " */", [
				['required', 'desc', 'string', 'doc comment'],
				['optional', 'limit', 'int', 'doc limit comment']
				]],
			["/**\n"
			. " * @argument string \$desc doc comment\n"
			. " * to more rows\n"
			. " * @optional int \$limit doc limit comment\n"
			. " */", [
				['required', 'desc', 'string', 'doc comment to more rows'],
				['optional', 'limit', 'int', 'doc limit comment']
				]],
			["/**\n"
			. " * @argument string \$desc doc comment\n"
			. " * to more rows\n"
			. " \n"
			. " * @optional int \$limit doc limit comment\n"
			. " */", [
				['required', 'desc', 'string', 'doc comment to more rows'],
				['optional', 'limit', 'int', 'doc limit comment']
				]],
			["/**\n"
			. " * @argument string \$desc doc comment\n"
			. " * to more rows\n"
			. " * \n"
			. " * and more...\n"
			. " * @optional int \$limit doc limit comment\n"
			. " */", [
				['required', 'desc', 'string', "doc comment to more rows\n and more..."],
				['optional', 'limit', 'int', 'doc limit comment']
				]],
			["/**\n"
			. "\t * @argument string \$desc doc comment\n"
			. "\t * to more rows\n"
			. "\t * \n"
			. "\t * and more...\n"
			. "\t * @optional int \$limit doc limit comment\n"
			. "\t */", [
				['required', 'desc', 'string', "doc comment to more rows\n and more..."],
				['optional', 'limit', 'int', 'doc limit comment']
				]],
			["/**\n"
			. "\t * @author abc def\nghch\n"
			. "\t */", [
				['author', 'abc def ghch']
				]],
		];
	}

}
