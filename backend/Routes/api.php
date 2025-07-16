<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST');


Router::post('/api/signup', [$services['auth'], 'signup']);
Router::post('/api/login', [$services['auth'], 'login']);
