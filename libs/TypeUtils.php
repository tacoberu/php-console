<?php
/**
 * Copyright (c) since 2004 Martin Takáč
 * @author Martin Takáč <martin@takac.name>
 */

namespace Taco\Console;

use RuntimeException;


class TypeUtils
{

	protected static $validators = [
		'bool' => 'is_bool',
		'boolean' => 'is_bool',
		'int' => 'is_int',
		'integer' => 'is_int',
		'float' => 'is_float',
		'number' => [__CLASS__, 'isNumber'],
		'numeric' => [__CLASS__, 'isNumeric'],
		'numericint' => [__CLASS__, 'isNumericInt'],
		'string' => 'is_string',
		'unicode' => [__CLASS__, 'isUnicode'],
		'array' => 'is_array',
		'list' => [__CLASS__, 'isList'],
		'object' => 'is_object',
		'resource' => 'is_resource',
		'scalar' => 'is_scalar',
		'callable' => [__CLASS__, 'isCallable'],
		'null' => 'is_null',
		'email' => [__CLASS__, 'isEmail'],
		'url' => [__CLASS__, 'isUrl'],
		'uri' => [__CLASS__, 'isUri'],
		'none' => [__CLASS__, 'isNone'],
		'type' => [__CLASS__, 'isType'],
		'identifier' => [__CLASS__, 'isPhpIdentifier'],
		'pattern' => null,
		'alnum' => 'ctype_alnum',
		'alpha' => 'ctype_alpha',
		'digit' => 'ctype_digit',
		'lower' => 'ctype_lower',
		'upper' => 'ctype_upper',
		'space' => 'ctype_space',
		'xdigit' => 'ctype_xdigit',
		'iterable' => [__CLASS__, 'isIterable'],
	];

	protected static $counters = [
		'string' => 'strlen',
		//~ 'unicode' => [__CLASS__::class, 'length'],
		'array' => 'count',
		'list' => 'count',
		'alnum' => 'strlen',
		'alpha' => 'strlen',
		'digit' => 'strlen',
		'lower' => 'strlen',
		'space' => 'strlen',
		'upper' => 'strlen',
		'xdigit' => 'strlen',
	];



	/**
	 * @param string
	 * @return OptionSignature::TYPE_*
	 */
	static function parseType($type)
	{
		switch (strtolower($type)) {
			case 'int':
			case 'integer':
				return OptionSignature::TYPE_INT;
			case 'string':
			case 'text':
				return OptionSignature::TYPE_TEXT;
			case 'float':
			case 'double':
			case 'number':
				return OptionSignature::TYPE_FLOAT;
			case 'bool':
			case 'boolean':
				return OptionSignature::TYPE_BOOL;
			default:
				if (substr($type, 0, 4) === 'enum') {
					return new TypeEnum(explode('|', substr($type, 5, -1)));
				}
				throw new RuntimeException("Unsupported type: `$type'.");
		}
	}



	/**
	 * @param mixed
	 * @return OptionSignature::TYPE_*
	 */
	static function inferType($val)
	{
		return self::parseType(gettype($val));
	}



	/**
	 * Throws exception if a variable is of unexpected type.
	 * @param  mixed
	 * @param  string  expected types separated by pipe
	 * @param  string  label
	 * @return void
	 *
	 * @credits by David Grudl, Nette foundation.
	 */
	static function assert($value, $expected, $label = 'variable')
	{
		if (!static::is($value, $expected)) {
			$expected = str_replace(['|', ':'], [' or ', ' in range '], $expected);
			if (is_array($value)) {
				$type = 'array(' . count($value) . ')';
			}
			elseif (is_object($value)) {
				$type = 'object ' . get_class($value);
			}
			elseif (is_string($value) && strlen($value) < 40) {
				$type = "string '$value'";
			}
			else {
				$type = gettype($value);
			}
			throw new RuntimeException("The $label expects to be $expected, $type given.");
		}
	}



	/**
	 * Finds whether a variable is of expected type.
	 * @param  mixed
	 * @param  string  expected types separated by pipe with optional ranges
	 * @return bool
	 *
	 * @credits by David Grudl, Nette foundation.
	 */
	static function is($value, $expected)
	{
		foreach (explode('|', $expected) as $item) {
			if (substr($item, -2) === '[]') {
				if (self::everyIs($value, substr($item, 0, -2))) {
					return true;
				}
				continue;
			}

			list($type) = $item = explode(':', $item, 2);
			if (isset(static::$validators[$type])) {
				if (!call_user_func(static::$validators[$type], $value)) {
					continue;
				}
			} elseif ($type === 'pattern') {
				if (preg_match('|^' . (isset($item[1]) ? $item[1] : '') . '\z|', $value)) {
					return true;
				}
				continue;
			} elseif (!$value instanceof $type) {
				continue;
			}

			if (isset($item[1])) {
				$length = $value;
				if (isset(static::$counters[$type])) {
					$length = call_user_func(static::$counters[$type], $value);
				}
				$range = explode('..', $item[1]);
				if (!isset($range[1])) {
					$range[1] = $range[0];
				}
				if (($range[0] !== '' && $length < $range[0]) || ($range[1] !== '' && $length > $range[1])) {
					continue;
				}
			}
			return true;
		}
		return false;
	}



	/**
	 * Finds whether a value is an integer or a float.
	 * @return bool
	 */
	static function isNumber($value)
	{
		return is_int($value) || is_float($value);
	}



	/**
	 * Finds whether a value is an integer.
	 * @return bool
	 */
	static function isNumericInt($value)
	{
		return is_int($value) || is_string($value) && preg_match('#^-?[0-9]+\z#', $value);
	}



	/**
	 * Finds whether a string is a floating point number in decimal base.
	 * @return bool
	 */
	static function isNumeric($value)
	{
		return is_float($value) || is_int($value) || is_string($value) && preg_match('#^-?[0-9]*[.]?[0-9]+\z#', $value);
	}



	/**
	 * Finds whether a value is a syntactically correct callback.
	 * @return bool
	 */
	static function isCallable($value)
	{
		return $value && is_callable($value, true);
	}



	/**
	 * Finds whether a variable is a zero-based integer indexed array.
	 * @return bool
	 */
	static function isList($value)
	{
		return is_array($value) && (!$value || array_keys($value) === range(0, count($value) - 1));
	}



	/**
	 * Finds whether all values are of expected type.
	 * @param  array|\Traversable
	 * @param  string  expected types separated by pipe with optional ranges
	 * @return bool
	 *
	 * @credits by David Grudl, Nette foundation.
	 */
	private static function everyIs($values, $expected)
	{
		if (!self::isIterable($values)) {
			return false;
		}
		foreach ($values as $value) {
			if (!static::is($value, $expected)) {
				return false;
			}
		}
		return true;
	}


}
