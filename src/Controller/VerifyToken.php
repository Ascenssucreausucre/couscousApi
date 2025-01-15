<?php

declare(strict_types=1); // strict mode

namespace App\Controller;



class CommentController extends Controller
{
    private function verifyToken()
    {
        try {
            // Récupérer l'en-tête Authorization
            $headers = getallheaders();
            $authorization = $headers['Authorization'] ?? null;

            if (!$authorization || !str_starts_with($authorization, 'Bearer ')) {
                throw new \Exception('Token non fourni ou mal formaté.');
            }

            // Extraire le token
            $token = substr($authorization, 7);

            // Clé secrète pour décoder le JWT
            $key = $_ENV['BEARER_TOKEN'] ?? null;

            if (!$key) {
                throw new \Exception('Clé secrète non disponible.');
            }

            // Décoder le JWT
            $decoded = Firebase\JWT\JWT::decode($token, new Firebase\JWT\Key($key, 'HS256'));

            // Retourner les données du token
            return $decoded;
        } catch (\Exception $e) {
            // Gérer les erreurs de validation
            http_response_code(401);
            echo json_encode(['error' => 'Token invalide : ' . $e->getMessage()]);
            exit(); // Terminer l'exécution si le token est invalide
        }
    }
}