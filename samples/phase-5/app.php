#!/bin/env php
<?php
/**
 * use php app.php --limit=50 app.php out.log
 */
if (@!include __DIR__ . '/vendor/autoload.php') {
	echo "Install packages using `composer install`!\n";
	exit(1);
}

require __dir__ . '/vendor/autoload.php';

$app = Taco\Console\AppFactory::create('0.0.1',
	'greating',
	'Ukázková aplikace. Smyslem je, definovat commandy bez žádného dalšího okecávání.

Commandy mohou a nemusí spolupracovat. Když spolupracují, tak můžeme použít továrničku, která nám usnadní vytvoření.
Všechny ty závislosti a přepínače se definují u commandu, a FW si je sám extrahuje.',
	null,
	[
		'Martin Takáč <martin@takac.name>',
		'Nermal Kowalski <kowalski@nermal.cz>',
	]
)->addCommand(App\Console\HelloCommand::class);
exit($app->run($GLOBALS));
