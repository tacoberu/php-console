<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


/**
 * @name version
 * Show version of application and exit.
 */
class VersionCommand implements Command
{

	private $output;

	private $version;


	/**
	 * Závislosti na služby. I výstup je služba.
	 * @param Output $output
	 * @param Version $version
	 */
	function __construct(Output $output, Version $version)
	{
		$this->output = $output;
		$this->version = $version;
	}



	/**
	 * Žádné options nejsou potřeba.
	 */
	function execute(Options $opts)
	{
		$this->output->notice('v' . (string) $this->version);
	}


}
