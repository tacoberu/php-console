<?php
/**
 * @author     Martin Takáč <martin@takac.name>
 * @copyright 2016 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Console;


/**
 * Kořenový kontainer aplikace.
 */
class StaticContainer implements Container
{

	/**
	 * @var object[]  storage for shared objects
	 */
	private $registry = [];

	/**
	 * @var object[]
	 */
	private $aliases = [];

	/**
	 * @var Resolver[]
	 */
	private $resolvers = [];

	/**
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 */
	function __construct($version, $name, $description, $epilog = Null, array $authors = Null)
	{
		$this->addInstance(Version::fromString($version));
		$this->addInstance(new AppInfo($name, $description, $epilog));
		if ($authors) {
			foreach ($authors as $author) {
				$this->addInstance($author);
			}
		}
	}



	/**
	 * Přidat službu, která bude dodávat implementace na požádání.
	 */
	function addResolver(Resolver $resolver)
	{
		$this->resolvers[] = $resolver;
	}



	/**
	 * Přiřadit objekt do kontaineru.
	 */
	function addInstance($inst)
	{
		// Pokud se jedná o popsaný objekt...
		if ($inst instanceof Describe) {
			$type = $inst->getType();
		}
		else {
			$type = get_class($inst);
		}

		// Vytvoření a zaregistrování základní instance
		if ( ! isset($this->registry[$type])) {
			$this->registry[$type] = [];
		}
		$this->registry[$type][] = $inst;

		// Aliasy podle interfaců.
		foreach (class_implements($type) as $x) {
			if ( ! isset($this->aliases[$x])) {
				$this->aliases[$x] = [];
			}
			$this->aliases[$x][] = $inst;
		}
	}



	/**
	 * Gets the service names of the specified type.
	 * @param  string
	 * @return string[]
	 */
	function findByType($class)
	{
		if ($class === get_class($this) || $class === Container::class) {
			return [ $this ];
		}

		if (isset($this->aliases[$class])) {
			return $this->aliases[$class];
		}

		if ( ! array_key_exists($class, $this->registry)) {
			if ($x = $this->resolveService($class)) {
				if ($x = $this->uniq($x)) {
					$this->addInstance($x);
				}
			}
		}

		if (isset($this->aliases[$class])) {
			return $this->aliases[$class];
		}

		if (isset($this->registry[$class])) {
			return $this->registry[$class];
		}

		return [];
	}



	/**
	 * Sestavení kontaineru. Vrací počet chyb. Vrátí-li prázdné pole,
	 * znamená to bez chyby. A můžem kontainer používát.
	 * @return array
	 */
	function check()
	{
		$xs = [];
		foreach ($this->registry as $type => $defs) {
			foreach ($defs as $index => $def) {
				if ($def instanceof Describe) {
					$state = $this->checkDescribe($def);
					if (count($state)) {
						$xs[self::formatClasses($type, $index)] = $state;
					}
				}
			}
		}
		return $xs;
	}



	/**
	 * @return array
	 */
	private function checkDescribe($def)
	{
		$xs = [];
		foreach ($def->getDepends() as $index => $type) {
			if (empty($this->findByType($type))) {
				$xs[$index] = $type;
			}
		}
		return $xs;
	}



	private function resolveService($class)
	{
		foreach ($this->resolvers as $resolver) {
			if ($inst = $resolver->resolve($class)) {
				return $inst;
			}
		}
	}



	private function uniq($inst)
	{
		if (is_callable($inst)) {
			return $inst($this);
		}

		return $inst;
	}



	private static function formatClasses($type, $index)
	{
		return "{$type}:{$index}";
	}

}
