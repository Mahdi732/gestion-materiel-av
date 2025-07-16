<?php

namespace Controllers;

use Repositories\UtilisateurRepository;
use Models\Utilisateur;

class AuthController {
    private UtilisateurRepository $repo;

    public function __construct (UtilisateurRepository $repo) {
        $this->repo = $repo;
    }

    public function signUp () : void {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data["nom"], $data["email"], $data["password"], $data["role_id"])) {
            http_response_code(400);
            echo json_encode(['error' => 'Champs manquants']);
            return;
        }

        if ($this->repo->findEmail($data['email'])) {
            http_response_code(409);
            echo json_encode(['error' => 'Email déjà utilisé']);
            return;
        }

        $user = new Utilisateur();
        $user->setNom($data['nom']);
        $user->setEmail($data['email']);
        $user->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));
        $user->setRoleId((int) $data['role_id']);

        $success = $this->repo->register($user);

        if ($success) {
        echo json_encode([
            'id' => $user->getId(),
            'nom' => $user->getNom(),
            'email' => $user->getEmail(),
        ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur lors de l\'enregistrement']);
        }
    }

    public function login(): void
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['email'], $data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Champs manquants']);
            return;
        }

        $user = $this->repo->login($data['email']);

        if (!$user || !password_verify($data['password'], $user->getPassword())) {
            http_response_code(401);
            echo json_encode(['error' => 'Email ou mot de passe invalide']);
            return;
        }

        echo json_encode([
            'id' => $user->getId(),
            'nom' => $user->getNom(),
            'email' => $user->getEmail(),
            'role_id' => $user->getRoleId(),
            'role_nom' => $user->getRoleNom()
        ]);
    }
}