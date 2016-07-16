<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 */

namespace Taco\Console;


use InvalidArgumentException;


/**
 * Formatter style class for defining styles.
 *
 * @author  Martin Takáč <martin@takac.name>
 * @credits Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Colors
{

	private static $availableForegroundColors = array(
		'black' => array('set' => 30, 'unset' => 39),
		'red' => array('set' => 31, 'unset' => 39),
		'green' => array('set' => 32, 'unset' => 39),
		'yellow' => array('set' => 33, 'unset' => 39),
		'blue' => array('set' => 34, 'unset' => 39),
		'magenta' => array('set' => 35, 'unset' => 39),
		'cyan' => array('set' => 36, 'unset' => 39),
		'white' => array('set' => 37, 'unset' => 39),
		'default' => array('set' => 39, 'unset' => 39),
	);

	private static $availableBackgroundColors = array(
		'black' => array('set' => 40, 'unset' => 49),
		'red' => array('set' => 41, 'unset' => 49),
		'green' => array('set' => 42, 'unset' => 49),
		'yellow' => array('set' => 43, 'unset' => 49),
		'blue' => array('set' => 44, 'unset' => 49),
		'magenta' => array('set' => 45, 'unset' => 49),
		'cyan' => array('set' => 46, 'unset' => 49),
		'white' => array('set' => 47, 'unset' => 49),
		'default' => array('set' => 49, 'unset' => 49),
	);

	private static $availableOptions = array(
		'bold' => array('set' => 1, 'unset' => 22),
		'underscore' => array('set' => 4, 'unset' => 24),
		'blink' => array('set' => 5, 'unset' => 25),
		'reverse' => array('set' => 7, 'unset' => 27),
		'conceal' => array('set' => 8, 'unset' => 28),
	);



	function apply($text, $fg = Null, $bg = Null, $opt = Null)
	{
		if ($fg == Null && $bg == Null && $opt == Null) {
			return $text;
		}
		$setCodes = array();
		$unsetCodes = array();

		if (null !== $fg) {
			if (! isset(self::$availableForegroundColors[$fg])) {
				throw new InvalidArgumentException("Unsuported foreground color: `$fg'.");
			}
			$setCodes[] = self::$availableForegroundColors[$fg]['set'];
			$unsetCodes[] = self::$availableForegroundColors[$fg]['unset'];
		}
		if (null !== $bg) {
			if (! isset(self::$availableBackgroundColors[$bg])) {
				throw new InvalidArgumentException("Unsuported background color: `$bg'.");
			}
			$setCodes[] = self::$availableBackgroundColors[$bg]['set'];
			$unsetCodes[] = self::$availableBackgroundColors[$bg]['unset'];
		}
		if (null !== $opt) {
			if (! isset(self::$availableOptions[$opt])) {
				throw new InvalidArgumentException("Unsuported option: `$opt'.");
			}
			$setCodes[] = self::$availableOptions[$opt]['set'];
			$unsetCodes[] = self::$availableOptions[$opt]['unset'];
		}

		return sprintf("\033[%sm%s\033[%sm", implode(';', $setCodes), $text, implode(';', $unsetCodes));
	}

}
