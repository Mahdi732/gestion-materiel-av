<?php

namespace Repositories;

use PDO;

class LocationRepository {

    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAll(): ?array {
        $stmt = $this->db->query("SELECT * FROM locations");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserReservations(int $clientId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM locations WHERE client_id = :client_id");
        $stmt->execute(['client_id' => $clientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare("
            INSERT INTO locations (client_id, materiel_id, date_debut, date_fin, statut)
            VALUES (:client_id, :materiel_id, :date_debut, :date_fin, :statut)
        ");
        return $stmt->execute([
            'client_id' => $data['client_id'],
            'materiel_id' => $data['materiel_id'],
            'date_debut' => $data['date_debut'],
            'date_fin' => $data['date_fin'],
            'statut' => $data['statut'] ?? 'en_attente',
        ]);
    }

    public function updateStatus(int $locationId, string $newStatus): bool {
        $stmt = $this->db->prepare("UPDATE locations SET statut = :statut WHERE id = :id");
        return $stmt->execute([
            'statut' => $newStatus,
            'id' => $locationId
        ]);
    }

    public function delete(int $locationId): bool {
        $stmt = $this->db->prepare("DELETE FROM locations WHERE id = :id");
        return $stmt->execute(['id' => $locationId]);
    }


    public function createContract(int $locationId, float $remise, float $tarifTotal): bool {
        $stmt = $this->db->prepare("
            INSERT INTO contrats_location (location_id, remise, tarif_total)
            VALUES (:location_id, :remise, :tarif_total)
        ");
        return $stmt->execute([
            'location_id' => $locationId,
            'remise' => $remise,
            'tarif_total' => $tarifTotal,
        ]);
    }


    public function createRetour(int $locationId, string $dateRetour, string $etatRetour): bool {
        $stmt = $this->db->prepare("
            INSERT INTO retours (location_id, date_retour, etat_retour)
            VALUES (:location_id, :date_retour, :etat_retour)
        ");
        return $stmt->execute([
            'location_id' => $locationId,
            'date_retour' => $dateRetour,
            'etat_retour' => $etatRetour,
        ]);
    }
}
