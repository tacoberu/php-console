<?php
/**
 * @author     Martin Takáč <martin@takac.name>
 * @copyright 2016 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Console;


use RuntimeException;


/**
 * Mějme objekt, a chceme u něj evidovat nějaké další zajímavé informace.
 */
class DescribedCommand implements Describe
{

	/**
	 * @var MetaInfo
	 */
	private $meta;

	/**
	 * @var array of string
	 */
	private $depends;

	/**
	 * @var OptionSignature
	 */
	private $signature;

	/**
	 * @var mixin
	 */
	private $invoker;

	/**
	 * @param string
	 * @param string
	 * @param array of string
	 * @param callable
	 */
	function __construct($name, $description, array $depends, array $args, $invoker)
	{
		$this->meta = (object) [
			'name' => $name,
			'description' => $description,
		];
		$this->depends = $depends;
		$this->signature = new OptionSignature();
		foreach ($args as $def) {
			switch ($def->type) {
				case 'require':
					$this->signature->addArgument($def->name, TypeUtils::parseType($def->validation), $def->description);
					break;
				case 'optional':
					$this->signature->addArgumentDefault($def->name, TypeUtils::parseType($def->validation), $def->default, $def->description);
					break;
				case 'flag':
					$this->signature->addFlag($def->name, $def->description);
					break;
				default:
					dump($def);
					throw new RuntimeException("Unsupported argument type: `{$def->name}'.");
			}
		}
		$this->invoker = $invoker;
	}



	/**
	 * @return OptionSignature
	 */
	function getOptionSignature()
	{
		return $this->signature;
	}



	/**
	 * @return mixin
	 */
	function getInvoker()
	{
		return $this->invoker;
	}



	/**
	 * @return array
	 */
	function check($container)
	{
		$xs = [];
		foreach ($this->depends as $index => $type) {
			if (empty($container->findByType($type))) {
				$xs[$index] = $type;
			}
		}
		return $xs;
	}



	function invoke($container, array $args = [])
	{
		foreach ($this->depends as $type) {
			$deps[] = reset(self::assertEmpty($container->findByType($type), $type));
		}
		call_user_func_array($this->invoker, array_merge($deps, $args));
	}



	/**
	 * @return array of string
	 */
	function getDepends()
	{
		return $this->depends;
	}



	/**
	 * @return MetaInfo
	 */
	function getMetaInfo()
	{
		return $this->meta;
	}



	/**
	 * @return string
	 */
	function getType()
	{
		return Command::class;
	}



	private static function assertEmpty($xs, $name)
	{
		if (empty($xs)) {
			throw new RuntimeException(sprintf('Not resolve argument: %s.', $name));
		}
		return $xs;
	}



	/**
	 * @param string
	 * @return OptionSignature::TYPE_*
	 */
	private static function parseType($type)
	{
		switch ($type) {
			case 'int':
			case 'integer':
				return OptionSignature::TYPE_INT;
			case 'string':
			case 'text':
				return OptionSignature::TYPE_TEXT;
			default:
				if (substr($type, 0, 4) === 'enum') {
					return new TypeEnum(explode('|', substr($type, 5, -1)));
				}
				throw new RuntimeException("Unsupported type: `$type'.");
		}
	}

}



/**
 * Mějme objekt, a chceme u něj evidovat nějaké další zajímavé informace.
 */
class DescribedDepends implements Describe
{

	/**
	 * @var MetaInfo
	 */
	private $meta;

	/**
	 * @var stdClass
	 */
	private $object;

	/**
	 * @var array of string
	 */
	private $depends;


	/**
	 * @param string
	 * @param string
	 * @param array string
	 * @param stdClass
	 */
	function __construct($name, $description, array $depends, $object)
	{
		$this->meta = (object) [
			'name' => $name,
			'description' => $description,
		];
		$this->depends = $depends;
		$this->object = $object;
	}



	function getObject()
	{
		return $this->object;
	}



	/**
	 * @return array of string
	 */
	function getDepends()
	{
		return $this->depends;
	}



	/**
	 * @return stdClass
	 */
	function getMetaInfo()
	{
		return $this->meta;
	}



	/**
	 * @return string
	 */
	function getType()
	{
		return get_class($this->object);
	}

}
