<?php

namespace Repositories;

use Interfaces\IMaterielRepository;
use Models\MaterielBase;
use Models\Camera;
use Models\Projecteur;
use Models\Microphone;
use PDO;

class MaterielRepository implements IMaterielRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    private function createMaterielFromRow(array $row): MaterielBase
    {
        switch ($row['type_id']) {
            case 1:
                $materiel = new Camera();
                break;
            case 2:
                $materiel = new Projecteur();
                break;
            case 3:
                $materiel = new Microphone();
                break;
            default:
                throw new \Exception("Type matériel inconnu : " . $row['type_id']);
        }

        $materiel->setId($row['id']);
        $materiel->setNom($row['nom']);
        $materiel->setMarque($row['marque']);
        $materiel->setModele($row['modele']);
        $materiel->setTypeId($row['type_id']);
        $materiel->setEtatId($row['etat_id']);
        $materiel->setDisponible((bool)$row['disponible']);
        $materiel->setCaracteristiques($row['caracteristiques']);

        return $materiel;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM materiels WHERE disponible = 1");
        $results = $stmt->fetchAll();

        $materiels = [];
        foreach ($results as $row) {
            $materiel = $this->createMaterielFromRow($row);
            $materiels[] = [
                'id' => $materiel->getId(),
                'nom' => $materiel->getNom(),
                'marque' => $materiel->getMarque(),
                'modele' => $materiel->getModele(),
                'type_id' => $materiel->getTypeId(),
                'etat_id' => $materiel->getEtatId(),
                'disponible' => $materiel->isDisponible(),
                'caracteristiques' => $materiel->getCaracteristiques()
            ];
        }

        return $materiels;
    }

    public function changerDisponibilite(int $id, bool $disponible): bool
    {
        $stmt = $this->db->prepare("UPDATE materiels SET disponible = :disponible WHERE id = :id");
        return $stmt->execute([
            'disponible' => $disponible,
            'id' => $id
        ]);
    }


    public function findById(int $id): ?MaterielBase
    {
        $stmt = $this->db->prepare("SELECT * FROM materiels WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $this->createMaterielFromRow($row);
    }

    public function save(MaterielBase $materiel): bool
    {
        if ($materiel->getId() === null) {
            $stmt = $this->db->prepare(
                "INSERT INTO materiels (nom, marque, modele, type_id, etat_id, disponible, caracteristiques)
                 VALUES (:nom, :marque, :modele, :type_id, :etat_id, :disponible, :caracteristiques)"
            );

            $result = $stmt->execute([
                'nom' => $materiel->getNom(),
                'marque' => $materiel->getMarque(),
                'modele' => $materiel->getModele(),
                'type_id' => $materiel->getTypeId(),
                'etat_id' => $materiel->getEtatId(),
                'disponible' => $materiel->isDisponible(),
                'caracteristiques' => $materiel->getCaracteristiques()
            ]);

            if ($result) {
                $materiel->setId((int)$this->db->lastInsertId());
            }

            return $result;
        } else {
            $stmt = $this->db->prepare(
                "UPDATE materiels SET
                    nom = :nom,
                    marque = :marque,
                    modele = :modele,
                    type_id = :type_id,
                    etat_id = :etat_id,
                    disponible = :disponible,
                    caracteristiques = :caracteristiques
                 WHERE id = :id"
            );

            return $stmt->execute([
                'nom' => $materiel->getNom(),
                'marque' => $materiel->getMarque(),
                'modele' => $materiel->getModele(),
                'type_id' => $materiel->getTypeId(),
                'etat_id' => $materiel->getEtatId(),
                'disponible' => $materiel->isDisponible(),
                'caracteristiques' => $materiel->getCaracteristiques(),
                'id' => $materiel->getId()
            ]);
        }
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM materiels WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
