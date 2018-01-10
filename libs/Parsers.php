<?php
/**
 * Copyright (c) since 2004 Martin Takáč
 * @author Martin Takáč <martin@takac.name>
 */

namespace Taco\Console;

use RuntimeException;


class Parsers
{

	function parseTypeSchema(array $defs)
	{
		$defs = array_map(function($x) {
			if ( ! is_array($x)) {
				$x = [$x, 'text', '~'];
			}
			if ( ! isset($x[1])) {
				$x[1] = 'text';
			}

			$name = array_shift($x);
			// optional
			// - name, default-value
			// - name, default-value, type
			// - name, default-value, type, doc-comment
			if ($name{0} === '?') {
				// type
				if ( ! isset($x[1])) {
					$x[1] = TypeUtils::inferType($x[0]);
				}

				$defaultvalue = array_shift($x);
				array_unshift($x, substr($name, 1));
				array_unshift($x, 'optional');

				// doc-comment
				if ( ! isset($x[3])) {
					$x[3] = '~';
				}

				// default value to end
				$x[] = $defaultvalue;
			}
			// required
			else {
				array_unshift($x, $name);
				array_unshift($x, 'required');
				if ( ! isset($x[3])) {
					$x[3] = '~';
				}
			}
			return $x;
		}, $defs);

		return $defs;
	}



	/**
	 * @param string
	 * @return array
	 */
	function parseSignatureFromDocComment($src)
	{
		if (empty($src) || strlen($src) < 5) {
			return [];
		}

		// Odříznout počáteční a koncové hvězdičky. Rozdělit na řádky.
		$xs = split("\n", trim(substr($src, 3, -2)));

		// Odstranit počáteční odsazení.
		$xs = array_map(function($x) {
			return ltrim($x, "*\t ");
		}, $xs);

		// Sloučit víceřádkové anotace.
		$prev = -1; // Tím, že unsetujeme indexi, tak nám tam zůstávají díry a $i - 1 do nich padaj.
		foreach ($xs as $i => $x) {
			// respektovat prázdnou řádku
			if (empty($x)) {
				if ($prev > -1) {
					$xs[$prev] .= "\n";
				}
				unset($xs[$i]);
				continue;
			}
			// volný text patřící předchozí anotaci
			if ($x{0} !== '@') {
				if ($prev > -1) {
					$xs[$prev] .= ' ' . trim($x);
					unset($xs[$i]);
					continue;
				}
				else {
					$xs[$i] = '@description ' . trim($x);
				}
			}
			$prev = $i;
		}

		$ret = [];
		foreach ($xs as $x) {
			if ($x{0} == '@') {
				$ret[] = self::parseAnnotation($x);
			}
		}

		return $ret;
	}



	/**
	 * @param string
	 * @return array
	 */
	private static function parseAnnotation($src)
	{
		switch (True) {
			case (substr($src, 0, 9) == '@argument'):
				$ret = self::parseAnnotationRow($src);
				array_unshift($ret, 'required');
				return $ret;
			case (substr($src, 0, 9) == '@optional'):
				$ret = self::parseAnnotationRow($src);
				array_unshift($ret, 'optional');
				return $ret;
			case (substr($src, 0, 7) == '@author'):
				return ['author', trim(substr($src, 7))];
			case (substr($src, 0, 12) == '@description'):
				return ['description', trim(substr($src, 12))];
		}
		throw new RuntimeException("Unsupported annotation: `$src'.");
	}



	/**
	 * @param string
	 * @return array
	 */
	private static function parseAnnotationRow($src)
	{
		$par = explode(' ', $src, 3);
		if ($par[2]{0} !== '$') {
			throw new RuntimeException("Illegal format of annotation: `$src'. Missing arg name.");
		}
		$type = $par[1];
		$par = explode(' ', $par[2], 2);
		$ret = [];
		$ret[1] = substr($par[0], 1);
		$ret[2] = $type;
		$ret[3] = trim($par[1]);
		return $ret;
	}


}
