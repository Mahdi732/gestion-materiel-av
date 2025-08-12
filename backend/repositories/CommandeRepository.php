<?php

namespace Repositories;

use Interfaces\ICommandeRepository;
use Models\CommandeVente;
use PDO;

class CommandeRepository implements ICommandeRepository {

    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    private function createCommandeFromRow(array $row): CommandeVente {
        $commande = new CommandeVente(
            json_decode($row['details'], true),
            (int)$row['client_id'],
            $row['paiement']
        );

        if (isset($row['id'])) {
            $commande->setId((int)$row['id']);
        }
        if (isset($row['statut'])) {
            $commande->setStatut($row['statut']);
        }
        return $commande;
    }

    public function getAll(): array {
        $query = "SELECT * FROM commandes";
        $stmt = $this->db->query($query);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $commandes = [];
        foreach ($rows as $row) {
            $commande = $this->createCommandeFromRow($row);
            $commandes[] = [
            'id'       => $commande->getId(),
            'details'  => $commande->getDetails(),
            'clientId' => $commande->getClientId(),
            'paiement' => $commande->getPaiement(),
            'statut'   => $commande->getStatut()
        ];
        }

        return $commandes;
    }

    public function findByUser(int $id): ?array {
            $query = "
                SELECT 
                    c.id AS commande_id,
                    c.details,
                    c.paiement,
                    c.statut,
                    c.date_creation
                FROM commandes c
                WHERE c.client_id = :id
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute(['id' => $id]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$rows) {
                return null;
            }

            foreach ($rows as &$row) {
                if ($row['details'] === 'Array') {
                    $row['details'] = '[]'; 
                }
                $row['details'] = is_array($row['details']) ? 
                    json_encode($row['details']) : 
                    $row['details'];
            }

            return $rows;
        }

    public function createFacture(int $commandeId, float $montant): bool {
        $query = "INSERT INTO factures (commande_id, montant) 
                VALUES (:commande_id, :montant)";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'commande_id' => $commandeId,
            'montant' => $montant
        ]);
    }


    public function changeStatus(int $commandeId, string $newStatus): bool {
        $query = "UPDATE commandes SET statut = :statut WHERE id = :id";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'statut' => $newStatus,
            'id' => $commandeId
        ]);
    }

    public function findById(int $id): ?array {
        $query = "SELECT * FROM commandes WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$rows) {
            return null;
        }

        $commandes = [];
        foreach ($rows as $row) {
            $commandes[] = $this->createCommandeFromRow($row);
        }

        return $commandes;
    }

    public function save(CommandeVente $commande): bool {
        $stmt = $this->db->prepare('INSERT INTO commandes (client_id, details, paiement, statut) VALUES (:client_id, :details, :paiement, :statut)');
        $result = $stmt->execute([
            'client_id' => $commande->getClientId(),
            'details' => json_encode($commande->getDetails()),
            'paiement' => $commande->getPaiement(),
            'statut' => $commande->getStatut()
        ]);

        if ($result) {
            $commande->setId((int)$this->db->lastInsertId());
        }

        return $result;
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM commandes WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
