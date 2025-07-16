<?php

use Core\Database;
use Repositories\UtilisateurRepository;
use Controllers\AuthController;

$db = Database::getConnection();
$utilisateurRepository  = new UtilisateurRepository ($db);
$authController = new AuthController ($utilisateurRepository);