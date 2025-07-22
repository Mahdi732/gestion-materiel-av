<?php

namespace Controllers;

use Services\GestionCatalogueService;
use Models\MaterielBase;
use Models\Camera;
use Models\Projecteur;
use Models\Microphone;

class MaterielController
{
    private GestionCatalogueService $catalogueService;

    public function __construct(GestionCatalogueService $service)
    {
        $this->catalogueService = $service;
    }

    public function getAll(): void
    {
        header('Content-Type: application/json');
        $materiels = $this->catalogueService->getAllMateriels();
        echo json_encode($materiels);
    }

    public function getById(int $id): void
    {
        header('Content-Type: application/json');
        $materiel = $this->catalogueService->getMaterielById($id);

        if (!$materiel) {
            http_response_code(404);
            echo json_encode(['error' => 'Matériel non trouvé']);
            return;
        }

        echo json_encode($materiel);
    }

    public function add(): void
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        $materiel = $this->createMaterielFromData($data);

        $success = $this->catalogueService->saveMateriel($materiel);

        if ($success) {
            echo json_encode(['message' => 'Matériel ajouté avec succès']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Échec de l\'ajout']);
        }
    }

    public function update(int $id): void
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        $existing = $this->catalogueService->getMaterielById($id);
        if (!$existing) {
            http_response_code(404);
            echo json_encode(['error' => 'Matériel non trouvé']);
            return;
        }

        $materiel = $this->createMaterielFromData($data);
        $materiel->setId($id);

        $success = $this->catalogueService->modifierMateriel($materiel);

        if ($success) {
            echo json_encode(['message' => 'Matériel mis à jour']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Échec de la mise à jour']);
        }
    }

    public function delete(int $id): void
    {
        header('Content-Type: application/json');

        $success = $this->catalogueService->supprimerMateriel($id);

        if ($success) {
            echo json_encode(['message' => 'Matériel supprimé']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Échec de la suppression']);
        }
    }

    private function createMaterielFromData(array $data): MaterielBase
    {
        switch ($data['type_id']) {
            case 1: $materiel = new Camera(); break;
            case 2: $materiel = new Projecteur(); break;
            case 3: $materiel = new Microphone(); break;
            default: $materiel = new MaterielBase(); break;
        }

        $materiel->setNom($data['nom'] ?? '');
        $materiel->setMarque($data['marque'] ?? '');
        $materiel->setModele($data['modele'] ?? '');
        $materiel->setTypeId($data['type_id'] ?? null);
        $materiel->setEtatId($data['etat_id'] ?? null);
        $materiel->setDisponible($data['disponible'] ?? true);
        $materiel->setCaracteristiques($data['caracteristiques'] ?? '');

        return $materiel;
    }
}
