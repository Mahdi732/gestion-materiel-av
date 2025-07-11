<?php

namespace Repositories;

use Models\Utilisateur;
use PDO;

class UtilisateurRepository {
    private PDO $db;

    
    public function __construct (PDO $db)
    {
        $this->db = $db;
    }

    public function register (Utilisateur $user) : bool {
        $sql = "INSERT INTO users (nom, email, password, role_id) VALUES (:nom, :email, :password, :role)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            'nom' => $user->getNom(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'role' => $user->getRoleId(),
        ]);
        return $result;
    }

    public function login (string $email) : ?Utilisateur {
        $sql = 'SELECT u.*, role.nom as role_nom
                FROM users u
                JOIN roles r ON u.role_id = r.id
                where u.email = :email';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return new Utilisateur($data);
        }

        return null;
    }

    public function findEmail (string $email) : bool {
        $sql = 'SELECT id FROM users WHERE email = :email';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);

        return $stmt->fetchColumn() !== false;
    }
}