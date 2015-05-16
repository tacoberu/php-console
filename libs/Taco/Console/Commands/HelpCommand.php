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

		if ($desc = $this->container->getApplicationDescription()) {
			$desc = "\n{$desc}\n";
		}

		$this->output->notice(strtr("%{appname} version: %{version}\n"
				. "%{appdescription}"
				. "\nUsage:\n  %{appname} <command> [--options...]\n"
				. "\nGlobal options:\n  --working-dir (-d)    If specified, use the given directory as working directory.\n"
				. "\nAvailable commands:\n%{available-commands}\n"
				. "\nAuthor:\n  %{author-name} <%{author-email}>",
				array(
			'%{appname}' => $this->container->getApplicationName(),
			'%{appdescription}' => $desc,
			'%{version}' => $this->container->getVersion(),
			'%{author-name}' => $this->container->getAuthor(),
			'%{author-email}' => $this->container->getAuthorEmail(),
			'%{available-commands}' => implode(PHP_EOL, $commands),
			)));
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
