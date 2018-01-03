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

$app = Taco\Console\SingleShell::create(
	/**
	 * @argument string $src Source file
	 * @argument string $desc Destination
	 * @optional int $limit How many characters to copy
	 */
	function($src, $desc, $limit = 10) {
		file_put_contents($desc, substr(file_get_contents($src), 0, $limit));
		return 0;
	},

	// optional
	"1.42",
	[
		'description' => "Lorem ipsum doler ist.",
		'authors' => [
			'John Dee <john@dee.tld>'
		]]
);
exit($app->fetch($GLOBALS));
