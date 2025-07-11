<?php

namespace Models;

class Utilisateur {
    private ?int $id;
    private string $nom;
    private string $email;
    private string $password;
    private ?int $roleId;
    private string $roleNom;

    public function __construct(array $data) 
    {
        $this->id = $data['id'];
        $this->nom = $data['nom'];
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->roleId = $data['role_id'];
        $this->roleNom = $data['role_nom'];
    }

    public function getId () : ?int {
        return $this->id;
    }

    public function setNom ($nom) : void {
        $this->nom = $nom;
    }

    public function getNom () : string {
        return $this->nom;
    }

    public function setEmail ($email) : void {
        $this->email = $email;
    }

    public function getEmail () : string {
        return $this->email;
    }

    public function setPassword ($password) : void {
        $this->password = $password;
    }

    public function getPassword () : string {
        return $this->password;
    }
    
    public function setRoleId ($roleId) : void {
        $this->roleId = $roleId;
    }

    public function getRoleId () : ?int {
        return $this->roleId;
    }

    public function setRoleNom ($roleNom) : void {
        $this->roleNom = $roleNom;
    }

    public function getRoleNom () : string {
        return $this->roleNom;
    }
}