<?php

/**
 * Copyright (c) 2004, 2015 Martin Takáč
 * @author     Martin Takáč <taco@taco-beru.name>
 */

require_once __dir__ . '/../vendor/autoload.php';

require_once __dir__ . '/Taco/Commands/interfaces.php';
require_once __dir__ . '/Taco/Commands/Output.php';
require_once __dir__ . '/Taco/Commands/Options.php';
require_once __dir__ . '/Taco/Commands/OptionItem.php';
require_once __dir__ . '/Taco/Commands/OptionSignature.php';
require_once __dir__ . '/Taco/Commands/RequestParser.php';
require_once __dir__ . '/Taco/Commands/Runner.php';


error_reporting(-1);

if (function_exists('ini_set')) {
    @ini_set('display_errors', 1);

    $memoryInBytes = function ($value) {
        $unit = strtolower(substr($value, -1, 1));
        $value = (int) $value;
        switch($unit) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }

        return $value;
    };

    $memoryLimit = trim(ini_get('memory_limit'));
    // Increase memory_limit if it is lower than 512M
    if ($memoryLimit != -1 && $memoryInBytes($memoryLimit) < 512 * 1024 * 1024) {
        @ini_set('memory_limit', '512M');
    }
    unset($memoryInBytes, $memoryLimit);
}
