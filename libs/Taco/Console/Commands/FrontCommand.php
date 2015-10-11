<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


/**
 * Default implementation for show help at all command.
 */
class FrontCommand implements TransactiableCommand
{

	private $output;
	private $request;
	private $container;
	private $origWorkDir;


	/**
	 * @param Output $output Where show documentation.
	 * @param Container $container Source of list of commands.
	 */
	function __construct(Output $output, Request $request, Container $container)
	{
		$this->output = $output;
		$this->request = $request;
		$this->container = $container;
	}



	/**
	 * @return string
	 */
	function getName()
	{
		return 'front';
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
		$sign = new OptionSignature();
		$sign->addOption('working-dir|d', $sign::TYPE_TEXT, function() {
			return self::formatRelativePath($this->request, getcwd());
		}, 'If specified, use the given directory as working directory.');
		return $sign;
	}



	/**
	 * @return int
	 */
	function execute(Options $options)
	{
		self::assertPathExists($options->getOption('working-dir'));
		$this->origWorkDir = getcwd();
		chdir($options->getOption('working-dir'));

		$command = $this->dispatchCommand();

		$this->request->applyRules($command->getOptionSignature());

		return $command->execute($this->request->getOptions());
	}



	/**
	 * Vrátit cestu zpět.
	 */
	function commit()
	{
		if ($this->origWorkDir) {
			chdir($this->origWorkDir);
		}
	}


	/**
	 * Vrátit cestu zpět.
	 */
	function rollback()
	{
		if ($this->origWorkDir) {
			chdir($this->origWorkDir);
		}
	}



	// PRIVATE ---------------------------------------------------------



	/**
	 * Výběr akce.
	 * @param Request $request
	 * @return Command
	 */
	private function dispatchCommand()
	{
		if (! $this->request->getOption('command')) {
			throw new RuntimeException("Not used command name. Try the command `help' to list all options.", 1);
		}
		return $this->container->getCommand($this->request->getOption('command'));
	}



	private static function assertPathExists($path)
	{
		if (! file_exists($path)) {
			throw new RuntimeException("Cannot switch to working directory `$path'.");
		}
	}



	private static function formatRelativePath($request, $path)
	{
		if ($request->getWorkingDir() == $path) {
			return '.';
		}
		if (Strings::startsWith($path, $request->getWorkingDir())) {
			return '.' . substr($path, strlen($request->getWorkingDir()));
		}
		return $path;
	}


}
