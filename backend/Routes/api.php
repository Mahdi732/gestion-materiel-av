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
Router::get('/api/materiels/select', [$services['catalogue'], 'getById']);
Router::post('/api/materiels', [$services['catalogue'], 'add']);
Router::post('/api/materiels/edit', [$services['catalogue'], 'update']);
Router::post('/api/materiels/delete', [$services['catalogue'], 'delete']);

//commande routing
Router::get('/api/commande', [$services['commande'], 'index']);
Router::post('/api/commande/user', [$services['commande'], 'userCommande']);
Router::post('/api/commande', [$services['commande'], 'create']);
Router::post('/api/commande/delete', [$services['commande'], 'delete']);
