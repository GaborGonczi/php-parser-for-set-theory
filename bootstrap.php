<?php

require_once 'vendor/phpunit.phar';

spl_autoload_register(function ($class) {

    if (strpos($class, 'Test') !== false) {

        $file = __DIR__ . '/tests/core/lib' . $class . '.php';
    } else {

        $file = __DIR__ . '/src/php/' . $class . '.php';
    }
    if (file_exists($file)) {
        require $file;
    }
});
