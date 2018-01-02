<?php
/**
 * Copyright (c) since 2004 Martin Takáč
 * @author Martin Takáč <martin@takac.name>
 */

namespace Taco\Console;


class RequestSelector
{

	/**
	 * @var Request
	 */
	private $request;


	static function fromGlobals(array $env)
	{
		$parser = new RequestEnvParser();
		$req = $parser->parse($env);

		$inst = new self($req);
		return $inst;
	}



	function __construct(Request $request)
	{
		$this->request = $request;
	}



	/**
	 * @param array of array
	 * @return array of mixed
	 */
	function select(array $defs)
	{
		// normalize
		$defs = Parsers::parseTypeSchema($defs);
		$signature = new OptionSignature();
		foreach ($defs as $item) {
			switch ($item[0]) {
				case 'optional':
					$signature->addArgumentDefault($item[1], TypeUtils::parseType($item[2]), $item[4], $item[3]);
					break;
				case 'required':
					$signature->addArgument($item[1], TypeUtils::parseType($item[2]), $item[3]);
					break;
			}
		}
		$this->request->applyRules($signature);

		$ret = [];
		foreach ($defs as $item) {
			$ret[] = $this->request->getOption($item[1]);
		}
		return $ret;
	}


}
