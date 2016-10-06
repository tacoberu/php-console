<?php
/**
 * Copyright (c) since 2004 Martin Takáč
 * @author Martin Takáč <martin@takac.name>
 */

namespace Taco\Console;


class Utils
{

	static function parseClassName($class, $postfix = null)
	{
		if ($index = strrpos($class, '\\')) {
			$class = substr($class, $index + 1);
		}
		if ($postfix && substr($class, -(strlen($postfix))) == $postfix) {
			$class = substr($class, 0, -(strlen($postfix)));
		}
		return strtolower($class);
	}

}
