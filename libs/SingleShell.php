<?php
/**
 * Copyright (c) since 2004 Martin Takáč
 * @author Martin Takáč <martin@takac.name>
 */

namespace Taco\Console;

use ReflectionFunction;
use RuntimeException;


class SingleShell
{

	const STATE_OK = 0;
	const STATE_UNCAUGHT_ERROR = 200;
	const STATE_UNKNOWN_ERROR = 254;


	/**
	 * @var OptionSignature
	 */
	private $signature;

	/**
	 * @var closure
	 */
	private $closure;


	/**
	 * @var string
	 */
	private $version;


	/**
	 * @var array
	 */
	private $options;


	static function create($closure, $version = '0.1', array $options = array())
	{
		$refl = new ReflectionFunction($closure);
		$args = [];
		foreach (Parsers::parseSignatureFromDocComment($refl->getDocComment()) as $def) {
			switch ($def[0]) {
				case 'required':
				case 'optional':
					$args[] = $def;
					break;
				case 'author':
					if ( ! isset($options['authors'])) {
						$options['authors'] = [];
					}
					$options['authors'][] = $def[1];
					$options['authors'] = array_unique($options['authors']);
					break;
				case 'description':
					$options['description'] = $def[1];
					break;
				default:
					throw new RuntimeException("Unsupported type: `{$def[0]}'.");
			}
		}

		foreach ($refl->getParameters() as $index => $parm) {
			if ( ! isset($args[$index])) {
				$args[$index] = [
					$parm->isOptional() ? 'optional' : 'required',
					$parm->getName(),
					'string',
					'~',
				];
			}
			if ($parm->isOptional() && isset($args[$index])) {
				$args[$index][4] = $parm->getDefaultValue();
			}
		}

		$signature = new OptionSignature();
		$signature->addFlag('help|h', 'Display this help message');
		$signature->addFlag('version|V', 'Display the application version');

		foreach ($args as $def) {
			switch ($def[0]) {
				case 'required':
					$signature->addArgument($def[1], TypeUtils::parseType($def[2]), $def[3]);
					break;
				case 'optional':
					$signature->addArgumentDefault($def[1], TypeUtils::parseType($def[2]), $def[4], $def[3]);
					break;
				default:
					throw new RuntimeException("Unsupported type: `{$def[0]}'.");
			}
		}

		$inst = new static($signature, $closure, $version, $options);
		return $inst;
	}



	function __construct(OptionSignature $signature, $closure, $version, array $options = array())
	{
		$this->signature = $signature;
		$this->closure = $closure;
		$this->version = $version;
		$this->options = $options;
	}



	/**
	 * Hodnoty prostředí. Takže typicky z GLOBALS, nebo cokoliv, co umí
	 * zpracovat nakonfigurovanej parser, viz: Container::getParser().
	 * @param array
	 */
	function fetch(array $env)
	{
		set_error_handler(function ($severity, $message, $file, $line) {
			if (($severity & error_reporting()) === $severity) {
				throw new \ErrorException($message, self::STATE_UNCAUGHT_ERROR, $severity, $file, $line);
			}
			return false;
		});

		try {
			$request = (new RequestEnvParser())->parse($env);
			$request->applyRules($this->signature);

			switch (True) {
				case $request->getOption('help'):
					return $this->fetchHelp($request);
				case $request->getOption('version'):
					return $this->fetchVersion();
				default:
					return $this->fetchClosure($request);
			}
		}
		catch (\Exception $e) {
			if (isset($output)) {
				$output->error($e->getMessage());
			}
			else {
				echo "Error: {$e->getMessage()}\n";
			}
			return ($e->getCode() > 0 ? $e->getCode() : self::STATE_UNKNOWN_ERROR);
		}
	}



	private function fetchClosure(Request $request)
	{
		$refl = new ReflectionFunction($this->closure);
		$args = [];
		foreach ($refl->getParameters() as $item) {
			$args[] = $request->getOption($item->getName());
		}
		return (int) $refl->invokeArgs($args);
	}



	private function fetchHelp(Request $request)
	{
		echo "{$request->getProgram()}, version: {$this->version}\n";
		if (isset($this->options['description'])) {
			echo "\n{$this->options['description']}\n";
		}
		echo "\n";
		echo "Usage:\n";
		echo "  {$request->getProgram()} [options]\n";
		echo "\n";
		echo "Options:\n";

		$pad = 2;
		$space = strlen(self::max(array_map(function($x) {
			return self::formatOptName($this->signature->getOption($x));
		}, $this->signature->getOptionNames())));

		$xs = array();
		foreach ($this->signature->getOptionNames() as $name) {
			$xs[] = self::formatOption($this->signature->getOption($name), $request, $space, $pad);
		}
		echo implode("\n", $xs);
		echo "\n";

		if (isset($this->options['authors'])) {
			$xs = array();
			echo "\nAuthors:\n";

			foreach ($this->options['authors'] as $author) {
				$xs[] = sprintf("%s%s",
					str_pad('', $pad),
					$author);
			}
			echo implode("\n", $xs);
			echo "\n";
		}

		echo "\n";

		return self::STATE_OK;
	}



	private function fetchVersion()
	{
		echo "{$this->version}\n";
		return self::STATE_OK;
	}



	/**
	 * @return string
	 */
	private static function formatOption(OptionItem $opt, Request $request, $space, $pad)
	{
		return sprintf("%s%-{$space}s %s%s",
				str_pad('', $pad),
				self::formatOptName($opt),
				$opt->getDescription(),
				self::formatDefaultValue($opt, $request)
				);
	}



	/**
	 * @return string
	 */
	private function formatOptName($opt)
	{
		$ret = '';
		if ($opt->getShortname()) {
			$ret .= '-' . $opt->getShortname() . ', ';
		}
		else {
			$ret .= '    ';
		}
		$ret .= '--' . $opt->getName();
		if ( ! $opt instanceof FlagOptionItem) {
			$ret .= '=<' . $opt->getType() . '>';
		}
		return $ret;
	}



	/**
	 * @return string
	 */
	private static function formatDefaultValue(OptionItem $opt, Request $request)
	{
		if ( ! $opt instanceof FlagOptionItem) {
			$name = $opt->getName();
			if ($request->isFilledOption($name)) {
				$val = $request->getOption($name);
				return " ({$val})";
			}
		}

		if ($val = $opt->getDefaultValue()) {
			return " ({$val})";
		}
	}



	/**
	 * Nevím proč mi najednou max nefachá...
	 * @TODO
	 * @return string
	 */
	private static function max(array $xs)
	{
		$m = null;
		foreach ($xs as $x) {
			if (strlen($x) > strlen($m)) {
				$m = $x;
			}
		}
		return $m;
	}

}
