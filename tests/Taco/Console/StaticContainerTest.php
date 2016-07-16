<?php
/**
 * @author     Martin Takáč <martin@takac.name>
 * @copyright 2016 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Console;

use PHPUnit_Framework_TestCase;
use RuntimeException;

/**
 * Kontainer obsahuje různé objekty, rozlišuje je podle typu.
 * @call phpunit StaticContainerTest.php
 */
class StaticContainerTest extends PHPUnit_Framework_TestCase
{

	function testEmpty()
	{
		$container = new StaticContainer('0.0-1', 'App', 'Empty container...');
		$this->assertEquals([new Version(0,0,1)], $container->findByType(Version::class));
		$this->assertCount(0, $container->findByType(Command::class));
	}



	function testOutput()
	{
		$container = new StaticContainer('0.0-1', 'App', 'Container with output...');
		$this->assertCount(0, $container->findByType(Output::class));
		$container->addInstance(new HumanOutput(new Stream));
		$this->assertCount(1, $container->findByType(Output::class));
	}



	function testManyOutputs()
	{
		$container = new StaticContainer('0.0-1', 'App', 'Container with output...');
		$container->addInstance(new HumanOutput(new Stream));
		$container->addInstance(new XmlOutput($this->getMockStream()));
		$this->assertCount(2, $container->findByType(Output::class));
	}



	function testCommand()
	{
		$container = new StaticContainer('0.0-1', 'App', 'Container with hand added command...');
		$this->assertCount(0, $container->findByType(Command::class));

		$orig = new DescribedCommand('version', "Show program's version number and exit.",
					[Output::class, Version::class],
					[],
			function ($output, $version) {
				$output->notice((string)$version);
			});
		$container->addInstance($orig);
		$container->addInstance(new HumanOutput(new Stream));

		$this->assertCount(1, $container->findByType(Command::class));
		$fn = reset($container->findByType(Command::class));
		$this->assertInstanceOf(DescribedCommand::class, $fn);
		$this->assertSame($orig, $fn);
	}



	function testCommands()
	{
		$container = new StaticContainer('0.0-1', 'App', 'Container with hand added command...');
		$this->assertCount(0, $container->findByType(Command::class));

		$orig = new DescribedCommand('version', "Show program's version number and exit.",
					[Output::class, Version::class],
					[],
			function ($output, $version) {
				$output->notice((string)$version);
			});
		$container->addInstance($orig);
		$container->addInstance(new DescribedCommand('help', "Show program's documentation and exit.",
					[Output::class],
					[],
			function ($output) {
				$output->notice('doc');
			}));
		$container->addInstance(new HumanOutput(new Stream));

		$this->assertCount(2, $container->findByType(Command::class));
		$fn = reset($container->findByType(Command::class));
		$this->assertInstanceOf(DescribedCommand::class, $fn);
		$this->assertSame($orig, $fn);
	}



	function ___testCommandFail()
	{
		$this->setExpectedException(RuntimeException::class, 'Not resolve argument: Taco\Console\Output.');

		$container = new StaticContainer('0.0-1', 'App', 'Container with hand added command...');
		$orig = new DescribedCommand('version', "Show program's version number and exit.",
					[Output::class, Version::class],
					[],
			function ($output, $version) {
				$output->notice($version);
			});
		$container->addInstance($orig);
		$fn = reset($container->findByType(Command::class));
	}



	function testCheckTrue()
	{
		$container = new StaticContainer('0.0-1', 'App', 'Container with hand added command...');
		$orig = new DescribedCommand('version', "Show program's version number and exit.",
					[Output::class, Version::class],
					[],
			function ($output, $version) {
				$output->notice($version);
			});
		$container->addInstance($orig);
		$container->addInstance(new HumanOutput(new Stream));
		$this->assertEquals([], $container->check());
	}



	function testCheckFalse()
	{
		$container = new StaticContainer('0.0-1', 'App', 'Container with hand added command...');
		$orig = new DescribedCommand('version', "Show program's version number and exit.",
					[Output::class, Version::class],
					[],
			function ($output, $version) {
				$output->notice($version);
			});
		$container->addInstance($orig);
		$this->assertEquals([
			'Taco\Console\Command:0' => ['Taco\Console\Output']
		], $container->check());
	}


	private function getMockStream()
	{
		return $this->getMockBuilder('Taco\Console\Stream')
				->getMock();
	}

}
