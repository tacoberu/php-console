<?php
/**
 * @author     Martin Takáč <martin@takac.name>
 * @copyright 2016 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Console;


use PHPUnit_Framework_TestCase;



/**
 * @call phpunit DescribedTest.php
 */
class DescribedTest extends PHPUnit_Framework_TestCase
{


	function testConstruct()
	{
		$m = new DescribedCommand('name', 'description', [], [], function() {});
		$this->assertState('name', 'description', [], $m);
	}



	private function assertState($name, $description, array $depends, $m)
	{
		$this->assertSame($name, $m->getMetaInfo()->name);
		$this->assertSame($description, $m->getMetaInfo()->description);
		//~ $this->assertSame($depends, $m->getMetaInfo()->depends);
	}

}
