<?php

namespace Controllers;

use Services\GestionCatalogueService;
use Services\ContratLocationService;

class LocationController
{
    private GestionCatalogueService $catalogueService;
    private ContratLocationService $contratService;

    public function __construct( GestionCatalogueService $catalogueService, ContratLocationService $contratService ) {
        $this->catalogueService = $catalogueService;
        $this->contratService = $contratService;
    }

    public function create(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);

        if (
            !$data 
            || !isset($data['materiel_id'], $data['client_id'], $data['date_debut'], $data['date_fin'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Données invalides ou manquantes']);
            return;
        }

        $dateDebut = new \DateTime($data['date_debut']);
        $dateFin = new \DateTime($data['date_fin']);
        $duree = $dateFin->diff($dateDebut)->days;

        if ($duree <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'La date de fin doit être après la date de début']);
            return;
        }

        $materiel = $this->catalogueService->getMaterielById((int)$data['materiel_id']);
        if (!$materiel) {
            http_response_code(404);
            echo json_encode(['error' => 'Matériel introuvable']);
            return;
        }

        $tarifTotal = $materiel->getPrixJour() * $duree;

        $locationCreated = $this->contratService->creerLocation([
            'materiel_id' => (int)$data['materiel_id'],
            'client_id' => (int)$data['client_id'],
            'date_debut' => $data['date_debut'],
            'date_fin' => $data['date_fin']
        ]);

        if (!$locationCreated) {
            http_response_code(500);
            echo json_encode(['error' => 'Échec de la création de la location']);
            return;
        }

        $this->catalogueService->changerDisponibilite((int)$data['materiel_id'], false);

        $this->contratService->creerContrat(
            $this->contratService->getLastLocationId(),
            0.0,
            $tarifTotal
        );

        echo json_encode([
            'message' => 'Location et contrat créés avec succès',
            'tarif_total' => $tarifTotal
        ]);
    }


    public function getAll(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $locations = $this->contratService->getLocations();

        echo json_encode($locations);
    }

    public function getByClient(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['client_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID client manquant']);
            return;
        }

        $locations = $this->contratService->getLocationsByClient((int)$data['client_id']);

        echo json_encode($locations);
    }

    public function updateStatus(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['location_id'], $data['statut'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Données manquantes']);
            return;
        }

        $success = $this->contratService->changerStatut((int)$data['location_id'], $data['statut']);

        if ($success) {
            echo json_encode(['message' => 'Statut mis à jour']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Échec mise à jour statut']);
        }
    }

    public function retour(): void
{
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['location_id'], $data['date_retour'], $data['etat_retour'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Données manquantes']);
        return;
    }

    $locationId = (int)$data['location_id'];
    $dateRetour = $data['date_retour'];
    $etatRetour = $data['etat_retour'];

    // 1️⃣ Save retour record
    $success = $this->contratService->enregistrerRetour($locationId, $dateRetour, $etatRetour);

    if (!$success) {
        http_response_code(500);
        echo json_encode(['error' => 'Échec de l’enregistrement du retour']);
        return;
    }

    $this->catalogueService->changerDisponibilite((int)$locationId, true);

    echo json_encode([
        'message' => 'Retour enregistré et matériel rendu disponible'
    ]);
}


}
