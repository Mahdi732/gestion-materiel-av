<?php

namespace Core;

use Exception;

class Autoloader {

    public static function register () : void {
        spl_autoload_register([self::class, 'autoload']);
    }

    public static function autoload (string $class) : void {
        $classPath = str_replace('\\', '/', $class);
        $file = __DIR__ . '/../' . $classPath . '.php';

        if (file_exists($file)) {
            require_once $file;
        }else {
            throw new Exception("Autoloader Error : class '$class' not found !");
        }
    }
}