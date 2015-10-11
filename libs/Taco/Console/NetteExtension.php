<?php
/**
 * This file is part of the Taco project (https://github.com/tacoberu)
 *
 * Copyright (c) 2004, 2011 Martin Takáč (http://martin.takac.name)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Taco\Console;


use Nette;
use Nette\PhpGenerator as Code;
use Nette\Utils\Validators;


/**
 * Potřebujeme zaregistrovat do containeru i sebe sama i vlastní request.
 */
class NetteExtension extends Nette\DI\CompilerExtension
{

	/**
	 * @var array
	 */
	private $defaults = [
			];


	/**
	 * Nette\DI\CompilerExtension
	 * - načtení dodatečné konfigurace
	 * - vytváření dalších služeb
	 */
	function loadConfiguration()
	{
		$config = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('container'))
			->setClass('Taco\\Console\\NetteContainer')
			->setDynamic(True);

		$builder->addDefinition($this->prefix('request'))
			->setClass('Taco\\Console\\Request')
			->setDynamic(True);
	}


}
