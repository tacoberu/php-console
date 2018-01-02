PHP Console
===========

Framework for a command line application with PHP. It's an extensible,
flexible component.

Concurent for Symfony/Console.

* Make command only with depends to interface.
* Support DI Container. Create command with DIC.
* Build-in support many output formats: colored, ansi, xml, json.
* Build-in default command: help, version.


# Development phase

## First phase

There is no time. I need to create a command line prompt quickly:

	<?php
	$limit = substr($argv[1], strlen('--limit='));
	$filesrc = $argv[2];
	$filedesc = $argv[3];

	file_put_contents($filedesc, substr(file_get_contents($filesrc), 0, $limit));

	exit(0);

I'll run:

    $ php app.php --limit=10 ./src.txt ./desc.txt

It works, cool. But what if I skip this parameter or move it? I mean, I can make some default value, right? And what if I write mistake? (What about documentation?)

    $ php app.php ./src.txt ./desc.txt
    $ php app.php ./src.txt ./desc.txt --limit=10
    $ php app.php --limit=all ./src.txt ./desc.txt
    $ php app.php -limit=-555 ./src.txt ./desc.txt

Ha!


## Second phase

Better way:

    composer require tacoberu/php-console

    <?php
    require __dir__ . '/vendor/autoload.php';

    $request = Taco\Console\RequestSelector::fromGlobals($GLOBALS);
    list($filesrc, $filedesc, $limit) = $request->select([
    	'src',
    	['desc', 'text'],
    	['?limit', 10, null, 'Počet znaků z obsahu.'],
    ]);

    file_put_contents($filedesc, substr(file_get_contents($filesrc), 0, $limit));

    exit(0);

This will work:

    $ php app.php ./src.txt ./desc.txt
    $ php app.php ./src.txt ./desc.txt --limit=10
    $ php app.php --limit=10 ./src.txt ./desc.txt # limit is optional and out of order
    $ php app.php --limit=all ./src.txt ./desc.txt # throws a error, 'all' is not a number
    $ php app.php --desc ./desc.txt ./src.txt

### What's the benefit of it?

- I can name the parameters. Thanks to that, I can throw them differently or, on the contrary, list them according to the position without the key.
- Parameters will be validated. So I get the number as a number.
- I can choose optional parameters.
- Finally, arguments can have different styles: with a space, an equator, a colon.


## The third phase

Probably better, but it still has some disadvantages. One of them is the treatment of errors. (Still missing documentation.) When some code drops an exception... well, it does not look nice. Let's try writing it differently.

    <?php
    require __dir__ . '/vendor/autoload.php';

    $app = Taco\Console\SingleShell::create(
        /**
         * @argument string $src Zdrojový soubor.
         * @argument string $desc Cílový soubor.
         * @optional int $limit Kolik se toho má kopírovat
         */
        function($src, $desc, $limit = 10) {
    		file_put_contents($desc, substr(file_get_contents($src), 0, $limit));
    		return 0;
        }
    );
    exit($app->fetch($GLOBALS));

This could be a complete application, including error capture.

### What's the benefit of it?

- Except for the previous one, he can list the version and help.
- It can catch the errors.
- Set the wrinkle level.

For example:

    $ php app.php --help
    $ php app.php --version
    $ php app.php -q ./src.txt ./desc.txt

----

I believe that a little tool, I believe, for the better works.
