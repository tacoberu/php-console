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
	private $request;
	private $frontCommand;
	private $container;


	/**
	 * @param Output $output Where show documentation.
	 * @param Container $container Source of list of commands.
	 */
	function __construct(Output $output, Request $request, FrontCommand $fc, Container $container)
	{
		$this->output = $output;
		$this->request = $request;
		$this->frontCommand = $fc;
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
				. "\nUsage:\n  %{program} <command> [--options...]\n"
				. "\nGlobal options:\n%{global-options}\n"
				. "\nAvailable commands:\n%{available-commands}\n"
				. "\nAuthor:\n  %{author-name} <%{author-email}>",
				array(
			'%{appname}' => $this->container->getApplicationName(),
			'%{program}' => basename($this->request->getProgram()),
			'%{appdescription}' => $desc,
			'%{version}' => $this->container->getVersion(),
			'%{author-name}' => $this->container->getAuthor(),
			'%{author-email}' => $this->container->getAuthorEmail(),
			'%{global-options}' => implode(PHP_EOL, self::formatOptionSignature($this->frontCommand->getOptionSignature())),
			'%{available-commands}' => implode(PHP_EOL, $commands),
			)));
	}



	/**
	 * @return string
	 */
	private static function formatCommand(Command $command)
	{
		$options = self::formatOptionSignature($command->getOptionSignature(), 4);
		return sprintf("  %-10s %s%s", $command->getName(),
				$command->getDescription(),
				count($options) ? PHP_EOL . implode(PHP_EOL, $options) : '');
	}



	/**
	 * @return list of string
	 */
	private static function formatOptionSignature(OptionSignature $sign, $pad = 2)
	{
		$options = array();
		foreach ($sign->getOptionNames() as $name) {
			$opt = $sign->getOption($name);
			$options[] = sprintf("%s--%-6s %-6s %s%s",
					str_pad('', $pad),
					$opt->getName(), '[' . $opt->getType() . ']', $opt->getDescription(), self::formatDefaultValue($opt));
		}
		return $options;
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
