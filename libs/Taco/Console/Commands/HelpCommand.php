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
		$appinfo = Utils::first($this->container->findByType(AppInfo::class));
		$version = Utils::first($this->container->findByType(Version::class));
		$request = Utils::first($this->container->findByType(Request::class));
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
		$this->output->notice(self::formatOptionSignature($request->getOptionSignature(), $request, '<fg=yellow>Global options:</>'));

		// Available commands
		$items = DictData::create('<fg=yellow>Available commands:</>');
		foreach ($this->container->findByType(Command::class) as $command) {
			list($name, $desc, $args) = self::formatCommand($command, $request);
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
	 * @return string
	 */
	private static function formatCommand(DescribedCommand $command, Request $request)
	{
		$options = self::formatOptionSignature($command->getOptionSignature(), $request, Null, 4);
		if ( ! $options->getItems()) {
			$options = NULL;
		}
		return [$command->getMetaInfo()->name, $command->getMetaInfo()->description, $options];
	}



	/**
	 * @return list of string
	 */
	private static function formatOptionSignature(OptionSignature $sign, Request $request, $groupName = Null, $pad = 2)
	{
		$options = DictData::create($groupName);
		foreach ($sign->getOptionNames() as $name) {
			list($name, $desc) = self::formatOption($sign->getOption($name), $request, 2);
			$options->add("<fg=green>$name</>", $desc);
		}
		return $options;
	}



	/**
	 * @return string
	 */
	private static function formatOption(OptionItem $opt, Request $request, $pad = 0, $space = 14)
	{
		return ['--' . $opt->getName(), sprintf("%-6s %s%s",
				'[' . $opt->getType() . ']',
				$opt->getDescription(),
				self::formatCurrentValue($opt, $request)
				)];
	}



	/**
	 * @return string
	 */
	private static function formatDefaultValue(OptionItem $opt, Request $request)
	{
		if ($val = $opt->getDefaultValue($request)) {
			return " <fg=yellow>[default: " . self::escape($val, $opt->getType()) . "]</>";
		}
	}



	/**
	 * @return string
	 */
	private static function formatCurrentValue(OptionItem $opt, Request $request)
	{
		if ($val = $opt->getValueFrom($request)) {
			return " <fg=yellow>[current: " . self::escape($val, $opt->getType()) . "]</>";
		}
	}



	/**
	 * @return string
	 */
	private static function formatDefaultValue2(OptionItem $opt, Request $request)
	{
		if ($val = $opt->getValueFrom($request)) {
			return "<fg=yellow>" . trim(self::escape($val, $opt->getType()), '"') . "</>";
		}
	}



	private static function escape($val, $type = Null)
	{
		switch ($type) {
			case 'bool':
				return ($val ? 'true' : 'false');
			case 'float':
			case 'int':
				return (string)$val;
			case 'text':
			default:
				return '"' . $val . '"';
		}
	}

}
