<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Musiques;

class MusiquesController extends Controller
{
    // Lister toutes les musiques
    public function index()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type");
        header("Content-Type: application/json");

        $musiques = Musiques::getInstance()->findAll();
        echo json_encode($musiques);
    }

    // Afficher une musique spécifique
    public function show($id)
    {
        $id = (int)$id;

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type");
        header("Content-Type: application/json");

        $musique = Musiques::getInstance()->find($id);

        if ($musique) {
            echo json_encode($musique);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Musique not found']);
        }
    }

    // Créer une nouvelle musique
    public function create()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type");
        header("Content-Type: application/json");

        if ($this->isPostMethod()) {
            $data = json_decode(file_get_contents('php://input'), true);

            if (isset($data['titre']) && isset($data['album_id']) && isset($data['artiste_id'])) {
                $musique = Musiques::getInstance()->create([
                    'titre' => $data['titre'],
                    'album_id' => $data['album_id'],
                    'artiste_id' => $data['artiste_id'],
                ]);
                http_response_code(201);
                echo json_encode($musique);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid input data']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
    }

    // Mettre à jour une musique
    public function update(int|string $id)
{
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");

    if ($_SERVER['REQUEST_METHOD'] !== 'PATCH') {
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed. Only PATCH is allowed.']);
        return;
    }

    $id = (int)$id;
    $musique = Musiques::getInstance()->find($id);

    if (!$musique) {
        http_response_code(404);
        echo json_encode(['error' => 'Musique not found']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $fieldsToUpdate = [];

    // Mise à jour de "titre" de la musique
    if (isset($data['title'])) {
        $fieldsToUpdate['titre'] = trim($data['title']);
    }

    // Mise à jour des informations de l'artiste
    if (isset($data['artist'])) {
        $fieldsToUpdate['artist'] = trim($data['artist']);
    }

    // Mise à jour des informations de l'album
    if (isset($data['album'])) {
        $fieldsToUpdate['album'] = trim($data['album']);
    }

    // Si aucun champ n'est fourni, retour erreur
    if (empty($fieldsToUpdate)) {
        http_response_code(400);
        echo json_encode(['error' => 'No fields to update']);
        return;
    }

    // Mise à jour de l'artiste si nécessaire
    if (isset($fieldsToUpdate['artist'])) {
        // Vérifiez si l'artiste existe
        $artiste = Artistes::getInstance()->findByName($fieldsToUpdate['artist']);
        if (!$artiste) {
            // Si l'artiste n'existe pas, le créer
            $artisteId = Artistes::getInstance()->create($fieldsToUpdate['artist']);
        } else {
            $artisteId = $artiste['id'];
        }

        // Ajouter l'artiste à la mise à jour des musiques
        $fieldsToUpdate['artiste_id'] = $artisteId;
    }

    // Mise à jour de l'album si nécessaire
    if (isset($fieldsToUpdate['album'])) {
        // Vérifiez si l'album existe
        $album = Albums::getInstance()->findByName($fieldsToUpdate['album']);
        if (!$album) {
            // Si l'album n'existe pas, le créer
            $albumId = Albums::getInstance()->create($fieldsToUpdate['album'], $artisteId);  // L'artiste_id peut être nécessaire ici
        } else {
            $albumId = $album['id'];
        }

        // Ajouter l'album à la mise à jour des musiques
        $fieldsToUpdate['album_id'] = $albumId;
    }

    // Mettre à jour la musique avec les nouvelles informations
    Musiques::getInstance()->update($id, $fieldsToUpdate);

    // Renvoyer la réponse après la mise à jour
    http_response_code(200);
    echo json_encode(['message' => 'Musique updated successfully']);
}


    // Supprimer une musique
    public function delete(int|string $id)
    {
        error_log("Traitement de la requête OPTIONS commencé.");

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        // Enregistrer les en-têtes envoyés dans le log
        error_log("Requête OPTIONS : En-têtes de la requête : " . json_encode(getallheaders()));
        
        // Ajout des en-têtes CORS
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");
        header("Content-Type: application/json");
    
        // Réponse à la requête OPTIONS
        http_response_code(200); // Code de réponse OK
        error_log("Requête OPTIONS : Réponse envoyée avec succès.");
        exit(0); // Fin de la requête ici
    }

        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed. Only DELETE is allowed.']);
            return;
        }

        $id = (int)$id;
        $musique = Musiques::getInstance()->find($id);

        if (!$musique) {
            http_response_code(404);
            echo json_encode(['error' => 'Musique not found']);
            return;
        }

        Musiques::getInstance()->delete($id);
        http_response_code(200);
        echo json_encode(['message' => 'Musique deleted successfully']);
    }
}
