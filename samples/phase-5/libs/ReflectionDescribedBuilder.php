<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Console;


use RuntimeException;
use ReflectionClass;


/**
 * Odekoruje command na základě anotací.
 * @author Martin Takáč <martin@takac.name>
 */
class ReflectionDescribedBuilder
{

	/**
	 * @param string Name of class.
	 * @return DescribedCommand
	 */
	static function buildCommand($klass)
	{
		$rc = new ReflectionClass($klass);
		$cnstr = $rc->getConstructor();

		$deps = [];
		foreach ($cnstr->getParameters() as $parm) {
			$deps[$parm->getName()] = $parm->getClass()->getName();
		}

		$def = self::parseSignatureFromDocComment($rc->getDocComment());
		if (empty($def->name)) {
			$def->name = strtr($klass, '\\', ':');
		}

		return new DescribedCommand($def->name, $def->description, $deps, $def->options, $klass);
	}



	private static function parseSignatureFromDocComment($src)
	{
		$ret = (object)[
			'name' => '',
			'description' => '',
			'options' => [],
		];
		$xs = split("\n", $src);
		$xs = array_slice($xs, 1, -1);
		$xs = array_map(function($x) {
			$prefix = substr($x, 0, 3);
			if ($prefix == ' * ' || $prefix == " *\t" || $prefix == '   ') {
				return substr($x, 3);
			}
			return $x;
		}, $xs);
		$doc = [];
		foreach ($xs as $x) {
			if ($x{0} == '@') {
				$par = self::parseAnnotation($x);
				if (empty($par)) {
					continue;
				}
				switch($par[0]) {
					case 'name':
						$ret->name = $par[1];
						break;
					case 'require':
						$ret->options[] = (object) array_combine(['type', 'validation', 'name', 'description'], $par);
						break;
					case 'optional':
						$ret->options[] = (object) array_combine(['type', 'validation', 'name', 'description', 'default'], $par);
						break;
					case 'flag':
						dump($par);
						die('=====[' . __line__ . '] ' . __file__);
						$ret->options[] = $par;
						break;
					default:
						dump($par);
				}
			}
			else {
				$doc[] = $x;
			}
		}
		$ret->description = implode(' ', $doc);

		return $ret;
	}



	/**
	 * @param string
	 * @return array
	 */
	private static function parseAnnotation($src)
	{
		if (substr($src, 0, 5) == '@name') {
			return ['name', trim(substr($src, 6))];
		}
		if (substr($src, 0, 9) == '@argument') {
			return array_merge(['require'], self::assertEmpty(json_decode('[' . substr($src, 10, -1) . ']'), substr($src, 6, -1)));
		}
		if (substr($src, 0, 9) == '@optional') {
			return array_merge(['optional'], self::assertEmpty(json_decode('[' . substr($src, 10, -1) . ']'), substr($src, 10, -1)));
		}
		if (substr($src, 0, 5) == '@flag') {
			return array_merge(['flag'], self::assertEmpty(json_decode('[' . substr($src, 6, -1) . ']'), substr($src, 6, -1)));
		}
		if (substr($src, 0, 7) == '@author') {
			return [];
		}
		throw new RuntimeException("Unsupported annotation: `$src'.");
	}



	private function assertEmpty($m, $label)
	{
		if (empty($m)) {
			throw new RuntimeException("Empty value for: $label.");
		}
		return $m;
	}
}
