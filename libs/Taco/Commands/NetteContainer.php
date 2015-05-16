<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Commands;


use Nette;


/**
 * ...
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
	 * @param string $name Name of command.
	 * @return Command with all dependencies.
	 */
	function getCommand($name)
	{
		try {
			return $this->getContainer()->getService("command.{$name}");
		}
		catch (Nette\DI\MissingServiceException $e) {
			throw new RuntimeException("Command `{$name}' not found.", 100, $e);
		}
	}



	function getOutput()
	{
		return $this->getContainer()->getByType("Taco\Commands\Output", True);
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
		$configurator = new Nette\Configurator;
		$configurator->setTempDirectory($this->tempDir);
		$configurator->addConfig($this->appconfigFile);
		return $configurator->createContainer();
	}


}