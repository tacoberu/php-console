<?php
/**
 * @copyright 2016 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Console;


/**
 * Shorthand for build standard application.
 *
 * @author Martin Takáč <martin@takac.name>
 */
class AppFactory
{

	/**
	 * @var Container
	 */
	private $container;


	static function create($version, $appname, $appdescription, $x, array $authors)
	{
		$container = new StaticContainer($version, $appname, $appdescription, null, array_map(function($x) {
			return Author::fromString($x);
		}, $authors));
		$container->addInstance(new HumanOutput(new Stream()));
		$container->addInstance(new XmlOutput(new Stream()));
		$container->addInstance(RequestEnvParser::createCommanded('help'));

		return new self($container);
	}



	function __construct(Container $container)
	{
		$this->container = $container;
	}



	function addCommand($cmdtype)
	{
		$this->container->addInstance(ReflectionDescribedBuilder::buildCommand($cmdtype));
		return $this;
	}



	function addInstance($inst)
	{
		$this->container->addInstance($inst);
		return $this;
	}



	function run(array $args, $defaultcommand = 'help')
	{
		$container = clone $this->container;

		$container->addInstance(ReflectionDescribedBuilder::buildCommand(VersionCommand::class));
		$container->addInstance(ReflectionDescribedBuilder::buildCommand(HelpCommand::class));
		$container->addInstance(ReflectionDescribedBuilder::buildCommand(ListCommand::class));

		$runner = new Runner($container);
		return $runner->run($args, $defaultcommand);
	}

}


