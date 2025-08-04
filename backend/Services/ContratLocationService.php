<?php

namespace Services;

use Repositories\LocationRepository;

class ContratLocationService
{
    private LocationRepository $locationRepo;

    public function __construct(LocationRepository $locationRepo)
    {
        $this->locationRepo = $locationRepo;
    }

    public function creerContrat(int $locationId, float $remise, float $tarifTotal): bool
    {
        return $this->locationRepo->createContract($locationId, $remise, $tarifTotal);
    }

    public function enregistrerRetour(int $locationId, string $dateRetour, string $etatRetour): bool
    {
        return $this->locationRepo->createRetour($locationId, $dateRetour, $etatRetour);
    }

    public function changerStatut(int $locationId, string $statut): bool
    {
        return $this->locationRepo->updateStatus($locationId, $statut);
    }

    public function getLocations(): ?array
    {
        return $this->locationRepo->getAll();
    }

    public function getLocationsByClient(int $clientId): ?array
    {
        return $this->locationRepo->getUserReservations($clientId);
    }

    public function creerLocation(array $data): bool
    {
        return $this->locationRepo->create($data);
    }
}
