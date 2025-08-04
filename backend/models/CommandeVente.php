<?php
namespace Models;

use Interfaces\ICommande;

class CommandeVente implements ICommande {
    private ?int $id = null;
    private array $details;
    private int $clientId;
    private string $paiement;
    private string $statut;   

    public function __construct(array $details, int $clientId, string $paiement) {
        $this->details = $details;
        $this->clientId = $clientId;
        $this->paiement = $paiement;
        $this->statut = 'en attente';
    }

    public function getId(): int {
        return $this->id;
    }
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getDetails(): array {
        return $this->details;
    }
    public function setDetails(array $details): void {
        $this->details = $details;
    }

    public function getClientId(): int {
        return $this->clientId;
    }
    public function setClientId(int $clientId): void {
        $this->clientId = $clientId;
    }

    public function getPaiement(): string {
        return $this->paiement;
    }
    public function setPaiement(string $paiement): void {
        $this->paiement = $paiement;
    }

    public function getStatut(): string {
        return $this->statut;
    }
    public function setStatut(string $statut): void {
        $this->statut = $statut;
    }
}
