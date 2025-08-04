<?php

namespace Services;

use Core\Database;
use Repositories\UtilisateurRepository;
use Controllers\AuthController;
use Repositories\MaterielRepository;
use Controllers\MaterielController;
use Controllers\VenteController;
use Repositories\CommandeRepository;

$db = Database::getConnection();
$utilisateurRepository  = new UtilisateurRepository ($db);
$authController = new AuthController ($utilisateurRepository);

$MaterielRepository = new MaterielRepository ($db);
$GestionCatalogueService = new GestionCatalogueService ($MaterielRepository);
$MaterielController = new MaterielController ($GestionCatalogueService);

$commandrepository = new CommandeRepository($db);
$commandeService = new GestionVente ($commandrepository);
$commandeController = new VenteController ($commandeService);

return [
    "auth" => $authController,
    "catalogue" => $MaterielController,
    "commande" => $commandeController
];