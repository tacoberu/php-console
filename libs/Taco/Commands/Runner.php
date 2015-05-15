<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Commands;


use Nette;
use Exception,
	RuntimeException;


class Runner
{

	private $tempDir;


	private $appconfigFile;


	/**
	 * @param string $appconfig Soubor obsahující definici služeb a akcí.
	 * @param string $tempDir Cesta k úložišti dočasných souborů.
	 */
	function __construct($appconfigFile, $tempDir)
	{
		$this->appconfigFile = $appconfigFile;
		$this->tempDir = $tempDir;
	}



	function run()
	{
		try {
			$request = RequestParser::fromEnv($GLOBALS);
			$command = $this->dispatchCommand($request);
			$options = Options::fromArray($request->args, self::assertType('Taco\Commands\OptionSignature', $command->getOptionSignature()));
			return $command->execute($options);
		}
		catch (Exception $e) {
			if (isset($protocolResolver, $request)) {
				$serializer = $protocolResolver->serializerByRequest($request);
				echo $serializer->formatError($output, $e);
			}
			else {
				echo $e->getMessage() . PHP_EOL;
			}
			return ($e->getCode() ? $e->getCode() : 254);
		}
	}



	/**
	 * Výběr akcí.
	 * @return Command
	 */
	private function dispatchCommand($request)
	{
		try {
			$container = $this->createContainer($request);
			return $container->getService("command.{$request->command}");
		}
		catch (Nette\DI\MissingServiceException $e) {
			throw new RuntimeException("Command `{$request->command}' not found.", 100, $e);
		}
	}



	private function createContainer()
	{
		$configurator = new Nette\Configurator;
		$configurator->setTempDirectory($this->tempDir);
		$configurator->addConfig($this->appconfigFile);
		return $configurator->createContainer();
	}



	private static function assertType($type, $value)
	{
		if ($value instanceof $type) {
			return $value;
		}
		throw new RuntimeException("invalid return type");
	}

}
