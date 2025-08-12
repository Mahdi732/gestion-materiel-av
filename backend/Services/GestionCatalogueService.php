<?php

namespace Services;

use Interfaces\IMaterielRepository;
use Models\MaterielBase;

class GestionCatalogueService
{
    private IMaterielRepository $materielRepo;

    public function __construct(IMaterielRepository $repo)
    {
        $this->materielRepo = $repo;
    }

    public function getAllMateriels(): array
    {
        return $this->materielRepo->getAll();
    }

    public function changerDisponibilite (int $id, bool $disponible) : bool {
        return $this->materielRepo->changerDisponibilite($id, $disponible);
    }

    public function getMaterielById(int $id): ?MaterielBase
    {
        return $this->materielRepo->findById($id);
    }

    public function saveMateriel(MaterielBase $materiel): bool
    {
        return $this->materielRepo->save($materiel);
    }

    public function modifierMateriel(MaterielBase $materiel): bool
    {
        return $this->materielRepo->save($materiel);
    }

    public function supprimerMateriel(int $id): bool
    {
        return $this->materielRepo->delete($id);
    }
}
