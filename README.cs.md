PHP Console
===========

Framework pro aplikaci z příkazového řádku v PHP. Snadno rozšiřitelná,
flexibilní komponenta.

Konkurence Symfony/Console.

* Command závislý přímo na rozhraní.
* Podpora DI kontaineru. Vytváření commandu s/z DIC.
* Vestavěná podpora pro vícero výstupních formátů: barvičky, ansi, xml, json.
* Vestavěné defaultní command: help, version.


# Fáze vývoje

## První fáze

Není čas. Potřebuji rychle vytvořit prográmek pro příkazovou řádku:

	<?php
	$limit = substr($argv[1], strlen('--limit='));
	$filesrc = $argv[2];
	$filedesc = $argv[3];

	file_put_contents($filedesc, substr(file_get_contents($filesrc), 0, $limit));

	exit(0);

Spuštění proběhne takto:

    $ php app.php --limit=10 ./src.txt ./desc.txt

Funguje, supr. Ale co když ten parametr vynechám, nebo jej přesunu? Přeci mohu udělat nějakou defaultní hodnotu, ne? A co když tam napíšu blbost? (A co třeba dokumentace?)

    $ php app.php ./src.txt ./desc.txt
    $ php app.php ./src.txt ./desc.txt --limit=10
    $ php app.php --limit=all ./src.txt ./desc.txt
    $ php app.php -limit=-555 ./src.txt ./desc.txt

Ha!


## Druhá fáze

Tak po lepším:

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

Bude fungovat toto:

    $ php app.php ./src.txt ./desc.txt
    $ php app.php ./src.txt ./desc.txt --limit=10
    $ php app.php --limit=10 ./src.txt ./desc.txt # limit je volitelný a je mimo pořadí
    $ php app.php --limit=all ./src.txt ./desc.txt # vyhodí chybu, 'all' není číslo
    $ php app.php --desc ./desc.txt ./src.txt # Tím, že 'desc' pojmenuju, tak jej mohu použít dřív. Ale k ostatním přistupuji jako k pozičním.

### Co získám?

- Parametry mohu pojmenovat. Díky tomu je mohu různě přehazovat, a nebo naopak je uvádět podle pozice bez klíče.
- Parametry budou validovány. Takže jako číslo dostanu opravdu číslo.
- Mohu mět volitelné parametry.
- A nakonec argumenty mohou mět různý styl: s mezerou, rovnítkem, dvojtečkou.


## Třetí fáze

Asi je to lepší, ale stále to má určité nevýhody. Jedna z nich je ošetření chyb. (Taky tam furt není ta dokumentace.) Když nám nějaký kód vyhodí výjimku... no, nevypadá to hezky. Zkusme to tedy zapsat ještě jinak.

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

Tím bychom mohli mět kompletní aplikaci včetně odchytávání chyb.

### Co to umí?

- Kromě předchozího to umí vypsat verzi a nápovědu.
- Umí to odchytávat chybovky.
- Nastavovat úroveň uřvanosti.

Pro příklad:

    $ php app.php --help
    $ php app.php --version
    $ php app.php -q ./src.txt ./desc.txt

----

Věřím, že drobný nástroj, věřím, že k lepšímu.
