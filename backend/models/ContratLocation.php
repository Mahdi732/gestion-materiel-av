<?php

namespace Entities;

use Interfaces\IContratLocation;

class ContratLocation implements IContratLocation
{
    private int $duree;
    private int $materielId;
    private int $clientId;
    private float $remise;
    private string $statutRetour;

    public function __construct(int $duree, int $materielId, int $clientId) {
        $this->duree = $duree;
        $this->materielId = $materielId;
        $this->clientId = $clientId;
        $this->remise = 0;
        $this->statutRetour = 'non retourné';
    }

    // Getters
    public function getDuree(): int
    {
        return $this->duree;
    }

    public function getClientId(): int
    {
        return $this->clientId;
    }

    public function getMaterielId(): int
    {
        return $this->materielId;
    }

    public function getRemise(): float
    {
        return $this->remise;
    }

    public function getStatutRetour(): string
    {
        return $this->statutRetour;
    }

    // Setters
    public function setDuree(int $duree): void
    {
        $this->duree = $duree;
    }

    public function setMaterielId(int $materielId): void
    {
        $this->materielId = $materielId;
    }

    public function setClientId(int $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function setRemise(float $remise): void
    {
        $this->remise = $remise;
    }

    public function setStatutRetour(string $statutRetour): void
    {
        $this->statutRetour = $statutRetour;
    }
}
