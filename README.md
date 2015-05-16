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
...


# Konkurence

https://github.com/c9s/CLIFramework - Dost velký.
https://github.com/Cilex/Cilex - Defakto postavené na symfony
https://github.com/symfony/Console - Vyzkoušeno, nedokonalé.
https://github.com/wp-cli/php-cli-tools - to fakt ne
http://laravel.com/docs/5.0/artisan - Laravel, ?
https://github.com/b-b3rn4rd/Terminalor - ?
http://seagullproject.org/ - ? 500


# TODO
1.	Nevynucovat akci, ale jak? Subpříkazy? command1:command2:command3, com1 com2 com3
2.	Krátké verze příkazů.
3.	Barvičky
4.	Validace typů. Ale zase to nepřehánět. Typ datum.
5.	Default command help, default command version.
6.	Parametr `name value` a parametr `name=value`,
7.	Kontainer nevyužívající NetteDI.
8.	Defaultní hodnoty jako callback.
