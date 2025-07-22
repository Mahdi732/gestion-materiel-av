<?php

use Core\Router; 

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

Router::post('/api/signup', [$services['auth'], 'signUp']);
Router::post('/api/login', [$services['auth'], 'login']);
