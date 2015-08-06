<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


require_once __dir__ . '/../../../vendor/autoload.php';


use PHPUnit_Framework_TestCase;



/**
 * Argument je buď poziční, a pak může ale nemusí být jmenován, ale musí být uvést.
 * Argument je volitelný, ale není poziční. Pak musí být uvdene jménem.
 *
 * - Volitelný parametru: musí být určen jménem. Nepodílí se na pořadí. Musí mět defaultní hodnotu.
 * - Poziční parametr s defaultní hodnotou: Musí být až jako na konec.
 * - Poziční parametr povinný: Určuje jméno.
 *
 * @call phpunit OptionsTest.php
 */
class RequestParserTest extends PHPUnit_Framework_TestCase
{


	function testEmptyWithDefault()
	{
		// Request reprezentuje vstupní data. Ta vůbec nemusí být najednou.
		$req = new Request('foo');

		// Data může zvalidovat
		$sig = new OptionSignature();
		$sig->addOption('working-dir', $sig::TYPE_TEXT, '.', '...');
		$sig->addArgumentDefault('task', $sig::TYPE_TEXT, 'run', '...');

		$req->applyRules($sig);
		$this->assertFalse($req->isMissingRules());

		$this->assertEquals([
				'working-dir' => '.',
				'task' => 'run'
				], $req->getOptions());
	}



	function testUne()
	{
		$raw1 = array(
				'help',
				);

		// Request reprezentuje vstupní data. Ta vůbec nemusí být najednou.
		$req = new Request('foo');

		$req->addRawData($raw1);

		// Data může zvalidovat
		$sig = new OptionSignature();
		$sig->addOption('working-dir', $sig::TYPE_TEXT, '.', '...');
		$sig->addArgumentDefault('task', $sig::TYPE_TEXT, 'run', '...');

		$req->applyRules($sig);
		$this->assertFalse($req->isMissingRules());

		$this->assertEquals([
				'task' => 'help',
				'working-dir' => '.',
				], $req->getOptions());
	}



	function testOverideDefaultValue()
	{
		$raw1 = array(
				'help',
				'--working-dir', '../..',
				);

		// Request reprezentuje vstupní data. Ta vůbec nemusí být najednou.
		$req = new Request('foo');

		$req->addRawData($raw1);

		// Data může zvalidovat
		$req->applyRules($this->getOptionSignatureFor('default-task'));
		$this->assertFalse($req->isMissingRules());

		$this->assertEquals([
				'task' => 'help',
				'working-dir' => '../..',
				], $req->getOptions());
	}



	function testIncrementalSetting()
	{
		$raw1 = array(
				'commit', // 1
				'--message', 'Lorem ipsum doler ist', // 3
				'ref-branche-name', // 2
				'--working-dir', '../..', // mimo pořadí
				'--version', '42', // mimo pořadí
				);

		// Request reprezentuje vstupní data. Ta vůbec nemusí být najednou.
		$req = new Request('foo');

		$req->addRawData($raw1);

		// Data může zvalidovat
		$sig = new OptionSignature();
		$sig->addOption('working-dir', $sig::TYPE_TEXT, '.', '...');
		$sig->addArgumentDefault('task', $sig::TYPE_TEXT, 'run', '...');
		$req->applyRules($sig);
		$this->assertTrue($req->isMissingRules());

		// Když už máme data nějak poskládaná, jsme schopni zjistit základní první informace.
		// Jméno akce je například před lokálními optiony. Globální můou být i za ní.
		// Jenže ty jsou už vyzobaný.
		$this->assertNotNull($req->getOption('task'));
		$this->assertEquals('commit', $req->getOption('task'));

		// Máme-li název commandu, jsme schopni zjistit jeho specielní nastavení
		// a to přiřadit do Requestu ke zpracování.
		$cmdRules = $this->getOptionSignatureFor($req->getOption('task'));

		// To opět můžeme použít pro validaci vstupních dat.
		$req->applyRules($cmdRules);

		// Nyní už máme kompletně zvalidovaná data.
		$this->assertFalse($req->isMissingRules());

		$this->assertEquals(array(
			'working-dir' => '../..',
			'task' => 'commit',
			'ref' => 'ref-branche-name',
			'message' => 'Lorem ipsum doler ist',
			'version' => 42,
		), $req->getOptions());
	}



	/**
	 * Přeskládat parametry.
	 * Klasika na příkladu funkce join
	 * def join(first, second, separator)
	 */
	function testPositionalArgument()
	{
		$req = new Request('foo');
		$req->addRawData(array(
				'John',
				'Dee',
				'--sep', ':',
				));

		$sig = new OptionSignature();
		$sig->addArgument('first', $sig::TYPE_TEXT, '...');
		$sig->addArgument('second', $sig::TYPE_TEXT, '...');
		$sig->addArgumentDefault('sep', $sig::TYPE_TEXT, '*', '...');

		$req->applyRules($sig);

		$this->assertEquals([
				'first' => 'John',
				'second' => 'Dee',
				'sep' => ':',
				], $req->getOptions());
	}



	/**
	 * Přeskládat parametry.
	 * Klasika na příkladu funkce join
	 * def join(first, second, separator)
	 */
	function testPositionalArgument2()
	{
		$req = new Request('foo');
		$req->addRawData(array(
				'John',
				'--sep', ':',
				'Dee',
				));

		$sig = new OptionSignature();
		$sig->addArgument('first', $sig::TYPE_TEXT, '...');
		$sig->addArgument('second', $sig::TYPE_TEXT, '...');
		$sig->addArgumentDefault('sep', $sig::TYPE_TEXT, '*', '...');

		$req->applyRules($sig);

		$this->assertEquals([
				'first' => 'John',
				'second' => 'Dee',
				'sep' => ':',
				], $req->getOptions());
	}




	// -- PRIVATE ------------------------------------------------------



	private function getOptionSignatureFor($name = Null)
	{
		switch ($name) {
			case 'commit':
				$sig = new OptionSignature();
				$sig->addOption('version', $sig::TYPE_INT, '0', '... taky by mohl být rozlišeno s hodnotu a bez hodnoty.');
				$sig->addArgument('ref', $sig::TYPE_TEXT, '...');
				$sig->addArgument('message', $sig::TYPE_TEXT, '...');
				return $sig;
			case 'person':
				$sig = new OptionSignature();
				$sig->addArgument('name', $sig::TYPE_TEXT, '...');
				$sig->addArgument('age', $sig::TYPE_INT, '...');
				return $sig;
			case 'generic-with-config':
				$sig = new OptionSignature();
				$sig->addArgument('working-dir', $sig::TYPE_TEXT, 'Jméno koho pozdravím.');
				$sig->addArgument('config', $sig::TYPE_TEXT, 'Jméno koho pozdravím.');
				return $sig;
			case 'default-task':
				$sig = new OptionSignature();
				$sig->addOption('working-dir', $sig::TYPE_TEXT, '.', 'Cesta k pracovnímu adresáři.');
				$sig->addArgumentDefault('task', $sig::TYPE_TEXT, 'run', '...');
				return $sig;
			default:
				$sig = new OptionSignature();
				$sig->addOption('working-dir', $sig::TYPE_TEXT, '.', 'Cesta k pracovnímu adresáři.');
				return $sig;
		}
	}

}
