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


	/**
	 * @param string $appconfig Soubor obsahující definici služeb a akcí.
	 * @param string $tempDir Cesta k úložišti dočasných souborů.
	 */
	function __construct($appconfigFile, $tempDir)
	{
		$this->appconfigFile = $appconfigFile;
		$this->tempDir = $tempDir;
	}



	/**
	 * Name of application.
	 * @return string
	 */
	function getApplicationName()
	{
		$name = isset($this->getContainer()->parameters['appname'])
			? $this->getContainer()->parameters['appname']
			: 'appname';
		return Strings::webalize($name);
	}



	/**
	 * Description of application.
	 * @return string
	 */
	function getApplicationDescription()
	{
		return isset($this->getContainer()->parameters['appdescription'])
			? $this->getContainer()->parameters['appdescription']
			: Null;
	}



	/**
	 * @return string
	 */
	function getAuthor()
	{
		return isset($this->getContainer()->parameters['author'])
			? $this->getContainer()->parameters['author']
			: 'Unknow';
	}


	/**
	 * @return string
	 */
	function getAuthorEmail()
	{
		return isset($this->getContainer()->parameters['email'])
			? $this->getContainer()->parameters['email']
			: 'Unknow';
	}


	/**
	 * @param string $name Name of command.
	 * @return Command with all dependencies.
	 */
	function getCommand($name)
	{
		try {
			switch ($name) {
				case 'version':
					return $this->getVersionCommand();
				case 'help':
					return $this->getHelpCommand();
				default:
					return $this->getContainer()->getService("command.{$name}");
			}
		}
		catch (Nette\DI\MissingServiceException $e) {
			throw new RuntimeException("Command `{$name}' not found.", 100, $e);
		}
	}



	/**
	 * Seznam všechn commandů, které jsou k dispozici.
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
			$cmd = $this->container->getService($name);
			$xs[$cmd->getName()] = $cmd;
		}
		return $xs;
	}



	/**
	 * @return Output
	 */
	function getOutput()
	{
		return $this->getContainer()->getByType("Taco\Console\Output", True);
	}



	/**
	 * @return Parser
	 */
	function getParser()
	{
		return $this->getContainer()->getByType("Taco\Console\RequestParser", True);
	}



	/**
	 * Verze aplikace.
	 * @return string 0.0.1
	 */
	function getVersion()
	{
		return isset($this->getContainer()->parameters['version'])
			? $this->getContainer()->parameters['version']
			: '0.0.1';
	}



	/**
	 * @return OptionSignature
	 */
	function getGenericSignature()
	{
		$sign = new OptionSignature();
		$sign->addOption('working-dir', '.', $sign::TYPE_TEXT, 'If specified, use the given directory as working directory.');
		return $sign;
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
		return $configurator->createContainer();
	}



	private function getVersionCommand()
	{
		return new VersionCommand($this->getOutput(), $this->getVersion());
	}



	private function getHelpCommand()
	{
		return new HelpCommand($this->getOutput(), $this);
	}

}
