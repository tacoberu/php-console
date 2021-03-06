<?php
/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč (martin@takac.name)
 */

namespace Taco\Console;


/**
 * Show help at all command.
 * @name help
 */
class HelpCommand implements Command
{

	/**
	 * @var Output
	 */
	private $output;

	/**
	 * @var Container
	 */
	private $container;


	/**
	 * @param Output $output Where show documentation.
	 * @param Container $container Source of list of commands.
	 */
	function __construct(Output $output, Container $container)
	{
		$this->output = $output;
		$this->container = $container;
	}



	/**
	 * @return int
	 */
	function execute(Options $opts)
	{
		// Hlavička
		$appinfo = reset($this->container->findByType(AppInfo::class));
		$version = reset($this->container->findByType(Version::class));
		$request = reset($this->container->findByType(Request::class));
		$this->output->notice(strtr("<fg=green>%{appname}</fg> version: <fg=yellow>%{version}</>\n",
			[
				'%{appname}' => $appinfo->getName(),
				'%{program}' => basename($request->getProgram()),
				'%{version}' => $version,
			]));

		// Popis
		if ($desc = $appinfo->getDescription()) {
			$this->output->notice($desc . "\n");
		}

		$this->output->notice(ListData::create('<fg=yellow>Usage:</>')
			->add(strtr("%{program} \<command> [--options...]", [
				'%{program}' => basename($request->getProgram()),
			])));

		// Global options
		$items = DictData::create('<fg=yellow>Global options:</>');
		foreach ($this->getGlobalOptions() as $option) {
			list($name, $desc) = self::formatOption($option, 2);
			$items->add("<fg=green>$name</>", $desc);
		}
		$this->output->notice($items);

		// Available commands
		$items = DictData::create('<fg=yellow>Available commands:</>');
		foreach ($this->container->findByType(Command::class) as $command) {
			list($name, $desc, $args) = self::formatCommand($command);
			$items->add("<fg=green>$name</>", $desc, $args);
		}
		$this->output->notice($items);

		// Authors
		$authors = $this->container->findByType(Author::class);
		if (count($authors)) {
			$items = ListData::create('<fg=yellow>Authors:</>');
			foreach ($authors as $x) {
				$items->add("<fg=green>{$x->getName()} <{$x->getEmail()}> </>");
			}
			$this->output->notice($items);
		}

		return 0;
	}



	// -- PRIVATE ------------------------------------------------------------



	/**
	 * @return [OptionItem]
	 */
	private function getGlobalOptions()
	{
		$outputs = $this->container->findByType(Output::class);
		$sign = $this->buildOutputOptionSignature($outputs);
		$ret = [];
		foreach ($sign->getOptionNames() as $name) {
			if ($name === 'command') {
				continue;
			}
			$ret[] = $sign->getOption($name);
		}

		return $ret;
	}



	/**
	 * @return string
	 */
	private static function formatCommand(DescribedCommand $command)
	{
		$options = self::formatOptionSignature($command->getOptionSignature(), 4);
		if ( ! $options->getItems()) {
			$options = NULL;
		}
		return [$command->getMetaInfo()->name, $command->getMetaInfo()->description, $options];
	}



	/**
	 * @return list of string
	 */
	private static function formatOptionSignature(OptionSignature $sign, $pad = 2)
	{
		$options = DictData::create(NULL);
		foreach ($sign->getOptionNames() as $name) {
			list($name, $desc) = self::formatOption($sign->getOption($name), 2);
			$options->add("<fg=green>$name</>", $desc);
		}
		return $options;
	}



	/**
	 * @return string
	 */
	private static function formatOption(OptionItem $opt, $pad = 0, $space = 14)
	{
		return ['--' . $opt->getName(), sprintf("%-6s %s%s%s",
				'[' . $opt->getType() . ']',
				$opt->getDescription(),
				self::formatDefaultValue($opt),
				$opt->hasDefaultValue() ? " <fg=blue>/volitelné</>" : ''
				)];
	}



	/**
	 * @return string
	 */
	private static function formatDefaultValue(OptionItem $opt)
	{
		if ($val = $opt->getDefaultValue()) {
			return " ({$val})";
		}
	}



	/**
	 * @return OptionSignature
	 */
	private function buildOutputOptionSignature(array $outputs)
	{
		$sgn = new OptionSignature;
		$xs = [];
		foreach ($outputs as $x) {
			$xs[] = Utils::parseClassName(get_class($x), 'Output');
		}
		$sgn->addOption('output', new TypeEnum($xs), reset($xs), 'Formát výstupu');
		return $sgn;
	}

}
