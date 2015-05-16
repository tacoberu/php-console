PHP Commands
============

Framework for a command line application with PHP. It's an extensible,
flexible component.

* Očekávám, že FW přidám jen seznam akcí. Žádné registrace ani nic podobného.
* Očekávám, že akce se budou konfigurovat via DI.
* Očekávám podporu pro různé formáty výstupu.
* Očekávám, že nebudu muset šahat do CLI-Frameworku
* Očekávám deklarativní zápis.

# Use
php-control-examples


# Konkurence

https://github.com/c9s/CLIFramework - Dost velký.
https://github.com/Cilex/Cilex - Defakto postavené na symfony
https://github.com/symfony/Console - Vyzkoušeno, nedokonalé.
https://github.com/wp-cli/php-cli-tools - to fakt ne
http://laravel.com/docs/5.0/artisan - Laravel, ?
https://github.com/b-b3rn4rd/Terminalor - ?
http://seagullproject.org/ - ? 500
https://phpconsole.com/ - ?
http://www.yiiframework.com/extension/php-console/
http://www.yiiframework.com/doc/guide/1.1/en/topics.console
https://github.com/barbushin/php-console
http://framework.zend.com/manual/current/en/modules/zend.console.introduction.html
http://etopian.com/software/php-cli-framework/


# TODO
2.	Krátké verze příkazů.
3.	Barvičky.
6.	Parametr `name value` a parametr `name=value`,
7.	Kontainer nevyužívající NetteDI.
8.	Defaultní hodnoty jako callback.
9.	Načítání configurace ze souboru +globální pro uživatele +globální pro systém
10.	Výstupy v různých formátech.
11.	Načítání stdin.
12.	Volitelná hlučnost. Logování do vícero zdrojů s různou hlučností.
11.	Lokalization
12.	Akce helper: Vícero autorů.
13.	Akce helper: Zarovnávání akcí a parametrů.
14. Konflikty mezi akcemi a jejich parametry. Například aby dva parametry jedné akce neměli stejný krátký název, nebo aby nekolidoval s jiným názvem.
15.	Symfony má pěknou funkčnost, kdy našeptává u překlepů.
16.	Subpříkazy? command1:command2:command3, com1 com2 com3
17.	Typ datum.
18.	Porovnání s Nette
19.	Porovnání s Symfony
20.	Working directory. Globální optiony.


# Changelog
5.	Default command help, default command version.
1.	Nevynucovat akci, ale jak?
4.	Validace typů. Ale zase to nepřehánět.
