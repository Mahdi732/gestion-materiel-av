<?php

namespace Controllers;

use Services\GestionCatalogueService;
use Models\Camera;
use Models\MaterielBase;

class MaterielController
{
    private GestionCatalogueService $catalogueService;

    public function __construct(GestionCatalogueService $catalogueService)
    {
        $this->catalogueService = $catalogueService;
    }

    public function index(): void
    {
        header('Content-Type: application/json');
        $materiels = $this->catalogueService->getAllMateriels();
        echo json_encode($materiels);
    }

    public function show(): void
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID manquant']);
            return;
        }

        $materiel = $this->catalogueService->getMaterielById((int) $id);

        if (!$materiel) {
            http_response_code(404);
            echo json_encode(['error' => 'Matériel non trouvé']);
            return;
        }

        echo json_encode($materiel);
    }

    public function store(): void
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['nom'], $data['marque'], $data['modele'], $data['type_id'], $data['etat_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Champs manquants']);
            return;
        }

        $materiel = new Camera();
        $materiel->setNom($data['nom']);
        $materiel->setMarque($data['marque']);
        $materiel->setModele($data['modele']);
        $materiel->setTypeId((int) $data['type_id']);
        $materiel->setEtatId((int) $data['etat_id']);
        $materiel->setDisponible(true);
        $materiel->setCaracteristiques($data['caracteristiques'] ?? '');

        try {
            $success = $this->catalogueService->saveMateriel($materiel);
            if ($success) {
                echo json_encode(['message' => 'Matériel ajouté avec succès']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur lors de l\'enregistrement']);
            }
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
