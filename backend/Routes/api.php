<?php

use Core\Router; 

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
// auth routing
Router::post('/api/signup', [$services['auth'], 'signUp']);
Router::post('/api/login', [$services['auth'], 'login']);

//categorie routing

Router::get('/api/materiels', [$services['catalogue'], 'getAll']);
Router::get('/api/materiels/:id', [$services['catalogue'], 'getById']);
Router::post('/api/materiels', [$services['catalogue'], 'add']);
Router::post('/api/materiels/:id', [$services['catalogue'], 'update']);
Router::post('/api/materiels/delete', [$services['catalogue'], 'delete']);
