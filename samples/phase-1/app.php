<?php
/**
 * use php app.php --limit=50 app.php out.log
 */

$limit = substr($argv[1], strlen('--limit='));
$filesrc = $argv[2];
$filedesc = $argv[3];

file_put_contents($filedesc, substr(file_get_contents($filesrc), 0, $limit));

exit(0);
