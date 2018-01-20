<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Console;


/**
 * Show list names of all available commands.
 * @name list
 * @author Martin Takáč <martin@takac.name>
 */
class ListCommand implements Command
{

	/**
	 * @var Output
	 */
	private $output;

	/**
	 * @var Container
	 */
	private $container;


	/**
	 * @param Output $output Where show documentation.
	 * @param Container $container Source of list of commands.
	 */
	function __construct(Output $output, Container $container)
	{
		$this->output = $output;
		$this->container = $container;
	}



	/**
	 * @return int
	 */
	function execute(Options $opts)
	{
		// Available commands
		$items = DictData::create(Null);
		foreach ($this->container->findByType(Command::class) as $command) {
			list($name, $desc) = self::formatCommand($command);
			$items->add("<fg=green>$name</>", $desc);
		}
		$this->output->notice($items);

		return 0;
	}



	// -- PRIVATE ------------------------------------------------------------



	/**
	 * @return string
	 */
	private static function formatCommand(DescribedCommand $command)
	{
		return [$command->getMetaInfo()->name, $command->getMetaInfo()->description];
	}

}
