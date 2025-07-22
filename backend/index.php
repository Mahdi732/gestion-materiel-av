<?php

require_once __DIR__ . '/Core/Autoloader.php';
require_once __DIR__ . '/Core/Router.php';
use Core\Autoloader;
use Core\Router;

Autoloader::register();

$services = require_once __DIR__ . '/Services/services.php';

require_once __DIR__ . '/Routes/api.php';

$uri = str_replace('/gestion-materiel/backend', '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$method = $_SERVER['REQUEST_METHOD'];

Router::resolve($method, $uri);