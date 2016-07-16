<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 */

namespace Taco\Console;


/**
 * Parsing text for tree. For even node can be definition attributes.
 *
 * @author  Martin Takáč <martin@takac.name>
 */
class MarkParser
{

	private $styles = [];

	/**
	 * @param string
	 * @return []
	 */
	function parse($message)
	{
		$message = (string) $message;
		$tagRegex = '[a-z][a-z0-9_=;-]*';
		if (! preg_match_all("#<(($tagRegex) | /($tagRegex)?)>#ix", $message, $matches, PREG_OFFSET_CAPTURE)) {
			return [$message];
		}

		$offset = 0;
		$stack = [];
		$root = (object)['content' => []];
		$cursor = $root;
		foreach ($matches[0] as $i => $match) {
			$pos = $match[1];
			$text = $match[0];

			// escaping
			if (0 != $pos && '\\' == $message[$pos - 1]) {
				continue;
			}

			// skip empty text.
			if ($pos > $offset) {
				$cursor->content[] = substr($message, $offset, $pos - $offset);
			}
			$offset = $pos + strlen($text);

			// opening tag?
			if ($open = '/' != $text[1]) {
				$style = $this->createStyleFromString(strtolower($matches[1][$i][0]));
				$node = (object) [
					'style' => $style,
					'content' => []
				];
				$cursor->content[] = $node;
				$stack[] = $cursor;
				$cursor = $node;
			}
			else {
				$cursor = array_pop($stack);
			}

		}

		// other
		$cursor->content[] = substr($message, $offset);

        return str_replace('\\<', '<', $root->content);
	}



    /**
     * Tries to create new style instance from string.
     *
     * @param string $string "fg=green;bg=white"
     *
     * @return [] | false
     */
    private function createStyleFromString($string)
    {
        if (isset($this->styles[$string])) {
            return $this->styles[$string];
        }

        if (!preg_match_all('/([^=]+)=([^;]+)(;|$)/', strtolower($string), $matches, PREG_SET_ORDER)) {
            return false;
        }

        $style = [];
        foreach ($matches as $match) {
			switch ($match[1]) {
				case 'fg':
					$style['fg'] = $match[2];
					break;
				case 'bg':
					$style['bg'] = $match[2];
					break;
				case 'options':
					$style['opt'] = $match[2];
					break;
				default:
					break;
			}
        }

        return $style;
    }

}
