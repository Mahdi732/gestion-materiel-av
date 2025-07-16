<?php

require_once __DIR__ . '/Core/Autoloader.php';
require_once __DIR__ . '/Core/Router.php';
use Core\Autoloader;
Autoloader::register();
use Core\Router;
$services = require_once __DIR__ . '/services.php';

require_once __DIR__ . '/api.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

Router::resolve($method, $uri);