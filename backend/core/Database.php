<?php

namespace Core;

use PDO;

class Database {
    private static ?PDO $instance = null;

    private function __construct () {}

    private static function getInstance () : PDO {
        if (self::$instance === null) {
            $host = '127.0.0.1';
            $db   = 'location_de_matériel';
            $user = 'root';
            $pass = '';
            $charset = 'utf8mb4';
        }
    }
}