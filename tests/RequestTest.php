<?php
/**
 * Copyright (c) since 2004 Martin Takáč
 * @author Martin Takáč <martin@takac.name>
 */

namespace Taco\Console;

use PHPUnit_Framework_TestCase;
use RuntimeException;


/**
 * Argument je buď poziční, a pak může ale nemusí být jmenován, ale musí být uvést.
 * Argument je volitelný, ale není poziční. Pak musí být uvdene jménem.
 *
 * - Volitelný parametru: musí být určen jménem. Nepodílí se na pořadí. Musí mět defaultní hodnotu.
 * - Poziční parametr s defaultní hodnotou: Musí být až jako na konec.
 * - Poziční parametr povinný: Určuje jméno.
 *
 * @call phpunit RequestTest.php
 */
class RequestTest extends PHPUnit_Framework_TestCase
{


	function testEmptyWithDefault()
	{
		// Request reprezentuje vstupní data. Ta vůbec nemusí být najednou.
		$req = new Request('foo', '/home');

		// Data může zvalidovat
		$sig = new OptionSignature();
		$sig->addOption('working-dir', $sig::TYPE_TEXT, '.', '...');
		$sig->addArgumentDefault('task', $sig::TYPE_TEXT, 'run', '...');

		$req->applyRules($sig);
		$this->assertFalse($req->isMissingRules());

		$this->assertEquals(new Options([
			'working-dir' => '.',
			'task' => 'run'
		]), $req->getOptions());
	}



	function testOneArgument()
	{
		$raw1 = array(
			'15',
		);

		// Request reprezentuje vstupní data. Ta vůbec nemusí být najednou.
		$req = new Request('foo', '/home');

		$req->addRawData($raw1);

		// Data může zvalidovat
		$sig = new OptionSignature();
		$sig->addOption('working-dir', $sig::TYPE_TEXT, '.', '...');
		$sig->addArgumentDefault('limit', $sig::TYPE_INT, 10, '...');

		$req->applyRules($sig);
		$this->assertFalse($req->isMissingRules());

		$this->assertEquals(new Options([
			'limit' => 15,
			'working-dir' => '.',
		]), $req->getOptions());
	}



	function testOverideDefaultValue()
	{
		$raw1 = array(
			'help',
			'--working-dir', '../..',
		);

		// Request reprezentuje vstupní data. Ta vůbec nemusí být najednou.
		$req = new Request('foo', '/home');

		$req->addRawData($raw1);

		// Data může zvalidovat
		$req->applyRules($this->getOptionSignatureFor('default-task'));
		$this->assertFalse($req->isMissingRules());

		$this->assertEquals(new Options([
			'task' => 'help',
			'working-dir' => '../..',
		]), $req->getOptions());
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
		$req = new Request('foo', '/home');

		$req->addRawData($raw1);

		// Data může zvalidovat
		$sig = new OptionSignature();
		$sig->addOption('working-dir', $sig::TYPE_TEXT, '.', '...');
		$sig->addArgumentDefault('task', $sig::TYPE_TEXT, 'run', '...');
		$req->applyRules($sig);
		$this->assertTrue($req->isMissingRules());

		// Když už máme data nějak poskládaná, jsme schopni zjistit základní první informace.
		// Jméno akce je například před lokálními optiony. Globální mohou být i za ní. Jsou už ale vyzobaný.
		$this->assertNotNull($req->getOption('task'));
		$this->assertEquals('commit', $req->getOption('task'));

		// Máme-li název commandu, jsme schopni zjistit jeho specielní nastavení
		// a to přiřadit do Requestu ke zpracování.
		$cmdRules = $this->getOptionSignatureFor($req->getOption('task'));

		// To opět můžeme použít pro validaci vstupních dat.
		$req->applyRules($cmdRules);

		// Nyní už máme kompletně zvalidovaná data.
		$this->assertFalse($req->isMissingRules());

		$this->assertEquals(new Options([
			'working-dir' => '../..',
			'task' => 'commit',
			'ref' => 'ref-branche-name',
			'message' => 'Lorem ipsum doler ist',
			'version' => 42,
		]), $req->getOptions());
	}



	/**
	 * Přeskládat parametry.
	 * Klasika na příkladu funkce join
	 * def join(first, second, separator)
	 */
	function testPositionalArgument()
	{
		$req = new Request('foo', '/home');
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

		$this->assertEquals(new Options([
			'first' => 'John',
			'second' => 'Dee',
			'sep' => ':',
		]), $req->getOptions());
	}



	/**
	 * Přeskládat parametry.
	 * Klasika na příkladu funkce join
	 * def join(first, second, separator)
	 */
	function testPositionalArgument2()
	{
		$req = new Request('foo', '/home');
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

		$this->assertEquals(new Options([
			'first' => 'John',
			'second' => 'Dee',
			'sep' => ':',
		]), $req->getOptions());
	}



	/**
	 * Přeskládat parametry.
	 * app -d ../ddd hello
	 */
	function testPositionalArgument3()
	{
		$req = new Request('app', '/home');
		$req->addRawData(array(
			'-d', '../ddd',
			'hello',
		));

		$sig = new OptionSignature();
		$sig->addArgumentDefault('command', $sig::TYPE_TEXT, 'run', '...');
		$sig->addOption('working-dir|d', $sig::TYPE_TEXT, '.', '...');

		$req->applyRules($sig);
		$this->assertEquals(new Options([
			'command' => "hello",
			"working-dir" => "../ddd",
		]), $req->getOptions());
	}



	/**
	 * Ověřit vyplněné parametry.
	 */
	function testIsFilled()
	{
		$this->setExpectedException('RuntimeException', "Missing required options:\n  --name  [text]  ...");

		$req = new Request('app', '/home');
		$req->addRawData(array(
			'-d', '../ddd',
			'hello',
		));

		$sig = new OptionSignature();
		$sig->addArgumentDefault('command', $sig::TYPE_TEXT, 'run', '...');
		$sig->addArgument('name', $sig::TYPE_TEXT, '...');
		$sig->addOption('working-dir|d', $sig::TYPE_TEXT, '.', '...');
		$sig->addArgumentDefault('age', $sig::TYPE_INT, 111, '...');

		$req->applyRules($sig);
		$this->assertFalse($req->isFilled());
		$req->getOptions();
	}



	/**
	 * Kontrolovat typy hodnot.
	 */
	function testValidateType()
	{
		$this->setExpectedException('UnexpectedValueException', "Option `age' has invalid type of value: `s55'. Except type: `int'.");
		$req = new Request('app', '/home');
		$req->addRawData(array(
			's55',
		));

		$sig = new OptionSignature();
		$sig->addArgument('age', $sig::TYPE_INT, '...');

		$req->applyRules($sig);
	}



	/**
	 * Vyžadovat pomlčky
	 * 	Když přepíšu --name dříve, než má pořadí, tak musím to pořadí nějak zohlednit.
	 *
	 * def name age name2 name3
	 * app hello -d ../.. --name Martin age 55
	 */
	function testPositionalArgument4()
	{
		$this->setExpectedException('UnexpectedValueException', "Option `age' has invalid type of value: `age'. Except type: `int'.");
		$req = new Request('app', '/home');
		$req->addRawData(array(
			'-d', '../ddd',
			'--name', 'John',
			'age', '55',
		));
		$sig = $this->getOptionSignatureFor('person');
		$req->applyRules($sig);
	}



	/**
	 * Vyžadovat pomlčky
	 * 	Když přepíšu --name dříve, než má pořadí, tak musím to pořadí nějak zohlednit.
	 *
	 * def name age name2 name3
	 * app hello -d ../.. --name Martin age 55
	 */
	function testPositionalArgument5()
	{
		$req = new Request('app', '/home');
		$req->addRawData(array(
			'--name3', 'Trois',
			'--name5', 'Cinq',
			'Une',
			'Deux',
			'Quatre',
			'Six',
		));

		$sig = new OptionSignature();
		$sig->addArgument('name1', $sig::TYPE_TEXT, '...');
		$sig->addArgument('name2', $sig::TYPE_TEXT, '...');
		$sig->addArgument('name3', $sig::TYPE_TEXT, '...');
		$sig->addArgument('name4', $sig::TYPE_TEXT, '...');
		$sig->addArgument('name5', $sig::TYPE_TEXT, '...');
		$sig->addArgument('name6', $sig::TYPE_TEXT, '...');

		$req->applyRules($sig);

		$this->assertEquals(new Options([
			'name1' => "Une",
			'name2' => "Deux",
			'name3' => "Trois",
			'name4' => "Quatre",
			'name5' => "Cinq",
			'name6' => "Six",
		]), $req->getOptions());
	}



	/**
	 * Pojmenované argumenty lze oddělovat rovnítkem.
	 * app hello --name=Trois age 55
	 */
	function testNamedWithEq()
	{
		$req = new Request('app', '/home');
		$req->addRawData(array(
			'--name=Trois',
			'--age', '55'
		));

		$sig = new OptionSignature();
		$sig->addArgument('name', $sig::TYPE_TEXT, '...');
		$sig->addArgument('age', $sig::TYPE_INT, '...');

		$req->applyRules($sig);

		$this->assertEquals(new Options([
			'name' => "Trois",
			'age' => 55,
		]), $req->getOptions());
	}



	/**
	 * Pojmenované argumenty lze oddělovat rovnítkem. Hodně podraz.
	 * app hello --name=Trois age 55
	 */
	function testNamedWithEqComplex()
	{
		$req = new Request('app', '/home');
		$req->addRawData(array(
			'--name=--name=foo',
			'--age', '55'
		));

		$sig = new OptionSignature();
		$sig->addArgument('name', $sig::TYPE_TEXT, '...');
		$sig->addArgument('age', $sig::TYPE_INT, '...');

		$req->applyRules($sig);

		$this->assertEquals(new Options([
			'name' => "--name=foo",
			'age' => 55,
		]), $req->getOptions());
	}



	function testIllegalOption()
	{
		$this->setExpectedException(RuntimeException::class, "Illegal option – `xyz'.");
		$req = new Request('app', '/home');
		$req->addRawData(array(
			'--age', '55'
		));

		$sig = new OptionSignature();
		$sig->addArgument('age', $sig::TYPE_INT, '...');

		$req->applyRules($sig);

		$req->getOption('xyz');
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
				$sig->addOption('working-dir|d', $sig::TYPE_TEXT, '.', 'Cesta k pracovnímu adresáři.');
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
