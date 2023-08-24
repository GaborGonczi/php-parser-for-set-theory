<?php

spl_autoload_register(function($element) {
    $file = dirname(__FILE__). '/src/php/' . $element . '.php';
    $file = str_replace('\\', '/', $file);
    if (file_exists($file) && is_readable($file)) {
        require_once $file;
    }
});