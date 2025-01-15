<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\User;
use Firebase\JWT\JWT;

class UserController extends Controller
{
    public function index()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type");
        header("Content-Type: application/json");

        $users = User::getInstance()->findAll();

        header("Content-Type: application/json");
        return $this->json(['success' => true, 'data' => $users]);
    }

    /**
     * Inscription d'un utilisateur.
     */
    public function register()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['error' => 'Méthode non autorisée.']);
        http_response_code(405);
        return;
    }

    if ($this->isPostMethod()) {
        // Récupérer les données JSON envoyées par la requête
        $data = json_decode(file_get_contents('php://input'), true);

        // Vérifiez si la décodification a échoué
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['error' => 'Données JSON invalides.']);
        }

        // Ajouter un log pour déboguer ce que contient $data
        error_log("Données reçues: " . print_r($data, true));  // Log les données reçues

        $username = $data['username'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$username || !$email || !$password) {
            return $this->json(['error' => 'Tous les champs sont requis.']);
        }

        try {
            // Vérification du format de l'email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("L'adresse email est invalide.");
            }

            // Vérification de la longueur du mot de passe
            if (strlen($password) < 8) {
                throw new \Exception("Le mot de passe doit contenir au moins 8 caractères.");
            }

            // Vérifier si l'utilisateur ou l'email existe déjà
            if (User::getInstance()->findOneBy(['username' => $username])) {
                throw new \Exception("Ce nom d'utilisateur est déjà utilisé.");
            }

            if (User::getInstance()->findOneBy(['email' => $email])) {
                throw new \Exception("Cette adresse email est déjà utilisée.");
            }

            // Hachage du mot de passe
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            // Création de l'utilisateur
            $userId = User::getInstance()->create([
                'username' => htmlspecialchars($username),
                'email' => htmlspecialchars($email),
                'password_hash' => $passwordHash,
            ]);

            return $this->json(['success' => true, 'user_id' => $userId]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()]);
        }
    }

    return $this->json(['error' => 'Requête non autorisée.']);
}



    /**
     * Connexion d'un utilisateur.
     */
    public function login()
    {
        if ($this->isPostMethod()) {
            $data = json_decode(file_get_contents('php://input'), true);

            $email = $data['email'] ?? null;
            $password = $data['password'] ?? null;

            if (!$email || !$password) {
                return $this->json(['error' => 'L\'email et le mot de passe sont requis.']);
            }

            try {
                // Trouver l'utilisateur par son email
                $user = User::getInstance()->findOneBy(['email' => $email]);

                if (!$user || !password_verify($password, $user['password_hash'])) {
                    throw new \Exception("Identifiants invalides.");
                }

                // Définir les données du payload du JWT
                $payload = [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'iat' => time(), // Timestamp de la création du token
                    'exp' => time() + 3600, // Expiration dans 1 heure
                ];

                // Clé secrète utilisée pour signer le JWT
                $key = $_ENV['BEARER_TOKEN'] ?? null; // Clé secrète, récupérée depuis .env ou définie dans un autre endroit

                if (!$key || !is_string($key)) {
                    throw new \Exception("La clé secrète pour le JWT est invalide.");
                }

                // Générer le JWT avec l'algorithme HS256
                $jwt = JWT::encode($payload, $key, 'HS256');  // Ajout de l'algorithme

                // Retourner le JWT en réponse
                return $this->json([
                    'success' => true,
                    'message' => 'Connexion réussie',
                    'token' => $jwt
                ]);

            } catch (\Exception $e) {
                return $this->json(['error' => $e->getMessage()]);
            }
        }

        return $this->json(['error' => 'Requête non autorisée.']);
    }

    /**
     * Obtenir les informations de l'utilisateur connecté.
     */
    public function getUserProfile(int $id)
    {
        if (!isset($_SESSION['user'])) {
            return $this->json(['error' => 'Non connecté.']);
        }

        try {
            $user = User::getInstance()->findOnBy(['user_id' => $id]);

            if (!$user) {
                return $this->json(['error' => 'Utilisateur introuvable.']);
            }

            return $this->json(['success' => true, 'data' => $user]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()]);
        }
    }
}
