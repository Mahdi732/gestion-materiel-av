<?php

namespace Controllers;

use Models\CommandeVente;
use Services\GestionVente;
use Services\Notification\NotificationEmail;
use Patterns\PaiementCB;
use Patterns\PaiementVirement;

class VenteController
{
    private GestionVente $gestionVente;

    public function __construct(GestionVente $gestionVente)
    {
        $this->gestionVente = $gestionVente;
        $this->gestionVente->ajouterObserver(new NotificationEmail());
    }

    public function index(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $commandes = $this->gestionVente->getCommandes();

        echo json_encode($commandes);
    }

    public function userCommande(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'];

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID manquant']);
            return;
        }

        $commandes = $this->gestionVente->getUserCommande((int) $id);

        echo json_encode($commandes);
    }

    public function create(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data || !isset($data['details'], $data['clientId'], $data['paiement'])) {
            http_response_code(400);
            echo json_encode([
                'error' => 'Données invalides ou manquantes',
                'data'=> $data
            ]);
            return;
        }

        $detailArray = json_decode($data['details'], true);

        if (!is_array($detailArray)) {
            http_response_code(400);
            echo json_encode(['error' => 'Détails invalides']);
            return;
        }

        $montantTotal = 0.0;
        foreach ($detailArray as $item) {
            if (isset($item['caracteristiques'])) {
                $montantTotal += (float) $item['caracteristiques'];
            }
        }

        $commande = new CommandeVente(
            $detailArray,
            (int)$data['clientId'],
            $data['paiement']
        );

        $paiementClass = 'Patterns\\' . $data['paiement'];
        if (!class_exists($paiementClass)) {
            http_response_code(400);
            echo json_encode(['error' => "Classe paiement inconnue: $paiementClass"]);
            return;
        }
        $paiementStrategire = new $paiementClass();

        $this->gestionVente->validerCommande($commande, $montantTotal, $paiementStrategire);

        echo json_encode([
            'message' => 'Commande traitée avec succès',
            'statut' => $commande->getStatut()
        ]);
    }

    public function delete(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'];

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID manquant']);
            return;
        }

        $success = $this->gestionVente->delete((int)$id);

        if ($success) {
            echo json_encode(['message' => 'Matériel supprimé']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Échec de la suppression']);
        }
    }
}

