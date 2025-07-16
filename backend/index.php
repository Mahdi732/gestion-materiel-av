<?php

require_once __DIR__ . '/Core/Autoloader.php';
require_once __DIR__ . '/Core/Router.php';
use Core\Autoloader;
Autoloader::register();
use Core\Router;



$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

Router::resolve($method, $uri);