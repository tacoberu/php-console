<?php
/**
 * Copyright (c) since 2004 Martin Takáč
 * @author Martin Takáč <martin@takac.name>
 */

namespace Taco\Console;

use PHPUnit_Framework_TestCase;


/**
 * @call phpunit UtilsTest.php
 */
class UtilsTest extends PHPUnit_Framework_TestCase
{

	function testConstruct()
	{
		$this->assertSame('utilstest', Utils::parseClassName(get_class($this)));
		$this->assertSame('utils', Utils::parseClassName(get_class($this), 'Test'));
	}

}
