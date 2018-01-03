<?php
/**
 * use php app.php --limit=50 app.php out.log
 */
require __dir__ . '/vendor/autoload.php';

$request = Taco\Console\RequestSelector::fromGlobals($GLOBALS);
list($filesrc, $filedesc, $limit) = $request->select([
	'src',
	['desc', 'text'],
	['?limit', 10, null, 'Počet znaků z obsahu.'],
]);

file_put_contents($filedesc, substr(file_get_contents($filesrc), 0, $limit));

exit(0);
