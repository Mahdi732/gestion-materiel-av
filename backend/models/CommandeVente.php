<?php

namespace Models;

use Interfaces\ICommande;

class CommandeVente implements ICommande
{
    private $client;
    private $produits;
    private $paiement;
    private $statut;
    private $dateCommande;

    public function __construct($client, array $produits, $paiement)
    {
        $this->client = $client;
        $this->produits = $produits;
        $this->paiement = $paiement;
        $this->statut = 'en_attente';
        $this->dateCommande = date('Y-m-d H:i:s');
    }

    public function getDetails(): array
    {
        return $this->produits;
    }

    public function getClient(): object
    {
        return $this->client;
    }

    public function getPaiement(): object
    {
        return $this->paiement;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function validerCommande(): void
    {
        $this->statut = 'validée';
        $this->paiement->effectuerPaiement(); // stratégie
    }

    public function getDateCommande(): string
    {
        return $this->dateCommande;
    }
}
