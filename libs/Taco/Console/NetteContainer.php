<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


use Nette,
	Nette\Utils\Strings;
use RuntimeException;


/**
 * Kontainer založený na Netťáckým DI kontaineru. Tudíž vyžaduje temp a config.neon.
 */
class NetteContainer implements Container
{

	private $appconfigFile;
	private $tempDir;
	private $container;
	private $origWorkDir;


	/**
	 * @param string $appconfig Soubor obsahující definici služeb a akcí.
	 * @param string $tempDir Cesta k úložišti dočasných souborů.
	 */
	function __construct($appconfigFile, $tempDir)
	{
		$this->appconfigFile = $appconfigFile;
		$this->tempDir = $tempDir;
	}



	function setRequest(Request $m)
	{
		$this->getContainer()->addService('console.request', $m);
		return $this;
	}


	/**
	 * Name of application.
	 * @iterface Container
	 * @return string
	 */
	function getApplicationName()
	{
		$name = isset($this->getContainer()->parameters['appname'])
			? $this->getContainer()->parameters['appname']
			: 'appname';
		return $name;
	}



	/**
	 * Description of application.
	 * @iterface Container
	 * @return string
	 */
	function getApplicationDescription()
	{
		return isset($this->getContainer()->parameters['appdescription'])
			? $this->getContainer()->parameters['appdescription']
			: Null;
	}



	/**
	 * @iterface Container
	 * @return string
	 */
	function getAuthor()
	{
		return isset($this->getContainer()->parameters['author'])
			? $this->getContainer()->parameters['author']
			: 'Unknow';
	}



	/**
	 * @iterface Container
	 * @return string
	 */
	function getAuthorEmail()
	{
		return isset($this->getContainer()->parameters['email'])
			? $this->getContainer()->parameters['email']
			: 'Unknow';
	}



	/**
	 * Můžeme to nahradit za jeden jedinej command. Protože všechny ostatní
	 * jsou jeho potomci.
	 *
	 * @iterface Container
	 * @return Command FrontCommand with all dependencies.
	 */
	function getFrontCommand()
	{
		return $this->getCommand('frontcommand');
	}



	/**
	 * @param string $name Name of command.
	 * @iterface Container
	 * @return Command with all dependencies.
	 * @TODO Odstranit ve prospěch FrontCommandu ?
	 */
	function getCommand($name)
	{
		$name = strtr($name, ':', '_');
		switch ($name) {
			case 'version':
				if ($this->getContainer()->hasService("command.{$name}")) {
					return $this->getContainer()->getService("command.{$name}");
				}
				return $this->createDefaultVersionCommand();
			case 'help':
				if ($this->getContainer()->hasService("command.{$name}")) {
					return $this->getContainer()->getService("command.{$name}");
				}
				return $this->createDefaultHelpCommand($this->getContainer()->getService("console.request"));
			default:
				try {
					return $this->getContainer()->getService("command.{$name}");
				}
				catch (Nette\DI\MissingServiceException $e) {
					throw new RuntimeException("Command `{$name}' not found.", 100, $e);
				}
		}
	}



	/**
	 * Seznam všech commandů, které jsou k dispozici.
	 * @iterface Container
	 * @return array of Command
	 */
	function getCommandList()
	{
		$xs = array();
		$cmd = $this->getCommand('version');
		$xs[$cmd->getName()] = $cmd;
		$cmd = $this->getCommand('help');
		$xs[$cmd->getName()] = $cmd;
		foreach ($this->container->findByType('Taco\Console\Command') as $name) {
			if ($name == 'frontcommand' || $name == 'command.frontcommand') {
				continue;
			}
			$cmd = $this->container->getService($name);
			$xs[$cmd->getName()] = $cmd;
		}
		return $xs;
	}



	/**
	 * @iterface Container
	 * @return Output
	 */
	function getOutput()
	{
		return $this->getContainer()->getByType("Taco\Console\Output", True);
	}



	/**
	 * @iterface Container
	 * @return RequestParser
	 */
	function getRequestParser()
	{
		return $this->getContainer()->getByType("Taco\Console\RequestParser", True);
	}



	/**
	 * Verze aplikace.
	 * @iterface Container
	 * @return string 0.0.1
	 */
	function getVersion()
	{
		return isset($this->getContainer()->parameters['version'])
			? $this->getContainer()->parameters['version']
			: '0.0.1';
	}



	// -- PRIVATE ------------------------------------------------------



	private function getContainer()
	{
		if (empty($this->container)) {
			$this->container = $this->createContainer();
		}
		return $this->container;
	}



	private function createContainer()
	{
		// existence uloženého adresáře
		if (!is_writable($this->tempDir)) {
			throw new RuntimeException("Directory `$this->tempDir' is missing or is not writable.");
		}

		// existence konfiguračního souboru
		$configurator = new Nette\Configurator;
		$configurator->setTempDirectory($this->tempDir);
		$configurator->addConfig($this->appconfigFile);
		$configurator->addParameters(['foo' => 1]);
		$configurator->addServices(['console.container' => $this]);
		return $configurator->createContainer();
	}



	private function createDefaultVersionCommand()
	{
		return new VersionCommand($this->getOutput(), $this->getVersion());
	}



	private function createDefaultHelpCommand($request)
	{
		return new HelpCommand($this->getOutput(), $request, $this->getFrontCommand(), $this);
	}




}
