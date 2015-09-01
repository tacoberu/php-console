<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


/**
 * Default implementation for show help at all command.
 */
class HelpCommand implements Command
{

	private $output;
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
	 * @return string
	 */
	function getName()
	{
		return 'help';
	}



	/**
	 * @return string
	 */
	function getDescription()
	{
		return 'This documentation.';
	}



	/**
	 * @return OptionSignature
	 */
	function getOptionSignature()
	{
		return new OptionSignature();
	}



	/**
	 * @return int
	 */
	function execute(Options $opts)
	{
		$commands = array();
		foreach ($this->container->getCommandList() as $command) {
			$commands[] = self::formatCommand($command);
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



	/**
	 * @return string
	 */
	private static function formatCommand(Command $command)
	{
		$options = array();
		foreach ($command->getOptionSignature()->getOptionNames() as $name) {
			$opt = $command->getOptionSignature()->getOption($name);
			$options[] = sprintf("    --%-6s %-6s %s%s", $opt->getName(), '[' . $opt->getType() . ']', $opt->getDescription(), self::formatDefaultValue($opt));
		}
		return sprintf("  %-10s %s%s", $command->getName(),
				$command->getDescription(),
				count($options) ? PHP_EOL . implode(PHP_EOL, $options) : '');
	}



	/**
	 * @return string
	 */
	private static function formatDefaultValue(OptionItem $opt)
	{
		if ($val = $opt->getDefaultValue()) {
			return " ({$val})";
		}
	}

}
