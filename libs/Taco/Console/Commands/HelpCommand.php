<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


use Nette;


/**
 * Reprezentuje popis závislost a implementaci vstupních bodů. Jeden kommand
 * je jedna akce, ale může být spouštěna ve více módech (dry-run, check a podobně)
 */
class HelpCommand implements Command
{

	private $output;
	private $container;


	/**
	 * Závislosti na služby. I výstup je služba-
	 */
	function __construct(Output $output, Container $container)
	{
		$this->output = $output;
		$this->container = $container;
	}



	/**
	 * Jméno akce
	 */
	function getName()
	{
		return 'help';
	}



	/**
	 * Popis
	 */
	function getDescription()
	{
		return 'Tato dokumentace.';
	}



	/**
	 * Definice pro nutné nastavení.
	 * @return OptionSignature
	 */
	function getOptionSignature()
	{
		return new OptionSignature();
	}



	/**
	 * Provede výkonný kód.
	 */
	function execute(Options $opts)
	{
		$commands = array();
		foreach ($this->container->getCommandList() as $command) {
			$commands[] = $this->formatCommand($command);
		}

		$this->output->notice("Ukázková aplikace pro runner

Runtime z composeru, vytvoření jen vlastních commandů a nette DI pro kompozici služeb.

Použití:
  appname <command> [--options...]

Příkazy:
" . implode(PHP_EOL, $commands) . "

Autor:
  Martin Takáč <martin@takac.name>
");
	}



	private function formatCommand($command)
	{
		$options = array();
		foreach ($command->getOptionSignature()->getOptionNames() as $name) {
			$opt = $command->getOptionSignature()->getOption($name);
			$options[] = sprintf("    --%-6s %-6s %s", $opt->getName(), '[' . $opt->getType() . ']', $opt->getDescription());
		}
		return sprintf("  %-10s %s%s", $command->getName(),
				$command->getDescription(),
				count($options) ? PHP_EOL . implode(PHP_EOL, $options) : '');
	}


}
