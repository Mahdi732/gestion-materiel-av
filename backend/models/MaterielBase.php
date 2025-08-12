<?php

namespace Models;

use Interfaces\IMateriel;

abstract class MaterielBase implements IMateriel
{
    protected ?int $id = null;
    protected string $nom;
    protected string $marque;
    protected string $modele;
    protected int $type_id;      
    protected int $etat_id;       
    protected bool $disponible;
    protected string $caracteristiques;

    // public function __construct( ?int $id, string $nom, string $marque, string $modele, int $type_id, int $etat_id, bool $disponible, string $caracteristiques) {
    //     $this->id = $id;
    //     $this->nom = $nom;
    //     $this->marque = $marque;
    //     $this->modele = $modele;
    //     $this->type_id = $type_id;
    //     $this->etat_id = $etat_id;
    //     $this->disponible = $disponible;
    //     $this->caracteristiques = $caracteristiques;
    // }


    public function getId(): ?int {
        return $this->id;
    }
    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getNom(): string {
        return $this->nom;
    }
    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    public function getMarque(): string {
        return $this->marque;
    }
    public function setMarque(string $marque): void {
        $this->marque = $marque;
    }

    public function getModele(): string {
        return $this->modele;
    }
    public function setModele(string $modele): void {
        $this->modele = $modele;
    }

    public function getTypeId(): int {
        return $this->type_id;
    }
    public function setTypeId(int $type_id): void {
        $this->type_id = $type_id;
    }

    public function getEtatId(): int {
        return $this->etat_id;
    }
    public function setEtatId(int $etat_id): void {
        $this->etat_id = $etat_id;
    }

    public function isDisponible(): bool {
        return $this->disponible;
    }
    public function setDisponible(bool $disponible): void {
        $this->disponible = $disponible;
    }

    public function getCaracteristiques(): string {
        return $this->caracteristiques;
    }
    public function setCaracteristiques(string $caracteristiques): void {
        $this->caracteristiques = $caracteristiques;
    }

    public function type(): ?string{
        return null; 
    }

    public function etat(): ?string{
        return null;
    }

    public function disponibilite(): bool{
        return $this->disponible;
    }

    public function getPrixJour () : float {
        return (float)$this->caracteristiques / 20;
    }
}
