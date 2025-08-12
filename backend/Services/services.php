<?php

namespace Services;

use Core\Database;
use Repositories\UtilisateurRepository;
use Controllers\AuthController;
use Controllers\LocationController;
use Repositories\MaterielRepository;
use Controllers\MaterielController;
use Controllers\VenteController;
use Repositories\CommandeRepository;
use Repositories\LocationRepository;

$db = Database::getConnection();
$utilisateurRepository  = new UtilisateurRepository ($db);
$authController = new AuthController ($utilisateurRepository);

$MaterielRepository = new MaterielRepository ($db);
$GestionCatalogueService = new GestionCatalogueService ($MaterielRepository);
$MaterielController = new MaterielController ($GestionCatalogueService);

$commandrepository = new CommandeRepository($db);
$commandeService = new GestionVente ($commandrepository);
$commandeController = new VenteController ($commandeService);

$locationRepository = new LocationRepository($db);
$locationServices = new ContratLocationService($locationRepository);
$locationController = new LocationController($GestionCatalogueService, $locationServices);

return [
    "auth" => $authController,
    "catalogue" => $MaterielController,
    "commande" => $commandeController,
    "location" => $locationController
];