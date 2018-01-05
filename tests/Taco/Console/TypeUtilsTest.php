<?php
/**
 * Copyright (c) since 2004 Martin Takáč
 * @author Martin Takáč <martin@takac.name>
 */

namespace Taco\Console;

use PHPUnit_Framework_TestCase;
use InvalidArgumentException;
use RuntimeException;


class TypeUtilsTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @dataProvider dataParseType
	 */
	function testParseType($expected, $tested)
	{
		$this->assertSame($expected, TypeUtils::parseType($tested));
	}



	function dataParseType()
	{
		return [
			[OptionSignature::TYPE_TEXT, 'text'],
			[OptionSignature::TYPE_TEXT, 'string'],
			[OptionSignature::TYPE_INT, 'int'],
			[OptionSignature::TYPE_INT, 'integer'],
			[OptionSignature::TYPE_FLOAT, 'float'],
			[OptionSignature::TYPE_FLOAT, 'double'],
			[OptionSignature::TYPE_FLOAT, 'number'],
			[OptionSignature::TYPE_BOOL, 'bool'],
			[OptionSignature::TYPE_BOOL, 'boolean'],
		];
	}



	/**
	 * @dataProvider dataInferType
	 */
	function testInferType($expected, $tested)
	{
		$this->assertSame($expected, TypeUtils::inferType($tested));
	}



	function dataInferType()
	{
		return [
			[OptionSignature::TYPE_TEXT, 'text'],
			[OptionSignature::TYPE_INT, 12],
			[OptionSignature::TYPE_FLOAT, 1.2],
			[OptionSignature::TYPE_BOOL, true],
		];
	}

}
