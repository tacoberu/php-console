<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


require_once __dir__ . '/../../../vendor/autoload.php';
require_once __dir__ . '/../../../libs/Taco/Console/RequestX.php';


use PHPUnit_Framework_TestCase;



/**
 * @call phpunit OptionsTest.php
 */
class RequestParserTest extends PHPUnit_Framework_TestCase
{


	function _testPositionalArgument()
	{
		$raw1 = array(
				'help',
				'commit',
				'--name', 'Martin',
				'--working-dir', '../..',
				'--age', '42',
				);

		// Request reprezentuje vstupní data. Ta vůbec nemusí být najednou.
		$req = new RequestX();

		$req->addRawData($raw1);

		// Data může zvalidovat
		$req->applyRules($this->getOptionSignatureFor('generic-with-pos'));
		$this->assertTrue($req->isMissingRules());

		// Když už máme data nějak poskládaná, jsme schopni zjistit základní první informace.
		// Jméno akce je například před lokálními optiony. Globální můou být i za ní.
		// Jenže ty jsou už vyzobaný.
		$this->assertTrue($req->hasCommandName());
		$this->assertEquals('help', $req->getCommandName());

		// Máme-li název commandu, jsme schopni zjistit jeho specielní nastavení
		//~ $cmdinst = $repo->getCommand($cmd);
		//~ $cmdRules = $cmdinst->getOptionSignature();
		$cmdRules = $this->getOptionSignatureFor($req->getCommandName());

		// To opět můžeme použít pro validaci vstupních dat.
		$req->applyRules($cmdRules);




print_r($req);
die('=====[' . __line__ . '] ' . __file__);

		// Nyní už máme kompletně zvalidovaná data.
		$this->assertFalse($req->isMissingRules());

		$this->assertEquals(array(
			'working-dir' => '../..',
			'name' => 'Martin',
			'age' => 42,
		), $req->getOptions());
	}



	function t_estPositionalArgument()
	{
		$req = new RequestX();
		$req->addRawData(array(
				'help',
				'--name', 'Martin',
				'--working-dir', '../..',
				'--age', '42',
				));
		$req->applyRules($this->getOptionSignatureFor('generic-with-pos'));
	}



	/**
	 * Chceme načísto soubor, ve kterém jsou teprve definice akcí.
	 */
	function testConfigFile()
	{
		$raw1 = array(
				'help',
				'--config', 'soubor.cfg',
				'--name', 'Martin',
				'--working-dir', '../..',
				'--age', '42',
				);
		// Request reprezentuje vstupní data. Ta vůbec nemusí být najednou.
		$req = new RequestX();
		$req->addRawData($raw1);

		// Data může zvalidovat
		$req->applyRules($this->getOptionSignatureFor('generic-with-config'));

		$this->assertEquals('soubor.cfg', $req->getOption('config'));
		// Nyní můžeme použít jméno souboru, načíst z něj definice a použít jej pro vytvoření dalších signatur.
	}



	/**
	 * Někdy prostě nemáme uvedenou žádnou akci. A chceme použít defaultní
	 * a k té defaultní se vztahují právě ty uvedené nastavení.
	 */
	function testDefaultAction()
	{
		$raw1 = array(
				'--name', 'Martin', // action option
				'--working-dir', '../..', // global option
				'--age', '42', // action option
				);
		// Request reprezentuje vstupní data. Ta vůbec nemusí být najednou.
		$req = new RequestX();
		$req->addRawData($raw1);

		// Data může zvalidovat
		$req->applyRules($this->getOptionSignatureFor('generic'));

		// Zde jsme přesvědčení, že už tam musí být akce. Pokud není, tak se rozhodnem pro defaultní.
		$this->assertFalse($req->hasCommandName());
		$req->setCommandName('help');
		$this->assertEquals('help', $req->getCommandName());
	}



	// -- PRIVATE ------------------------------------------------------



	private function getOptionSignatureFor($name = Null)
	{
		switch ($name) {
			case 'help':
				$sig = new OptionSignature();
				$sig->addArgument('name', $sig::TYPE_TEXT, '');
				$sig->addArgument('age', $sig::TYPE_INT, '');
				return $sig;
			case 'generic-with-config':
				$sig = new OptionSignature();
				$sig->addArgument('working-dir', $sig::TYPE_TEXT, 'Jméno koho pozdravím.');
				$sig->addArgument('config', $sig::TYPE_TEXT, 'Jméno koho pozdravím.');
				return $sig;
			case 'generic-with-pos':
				$sig = new OptionSignature();
				$sig->addArgument('working-dir', $sig::TYPE_TEXT, 'Jméno koho pozdravím.');
				$sig->addPositional('action', $sig::TYPE_TEXT, '...');
				return $sig;
			default:
				$sig = new OptionSignature();
				$sig->addArgument('working-dir', $sig::TYPE_TEXT, 'Jméno koho pozdravím.');
				return $sig;
		}
	}

}
