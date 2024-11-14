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
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: POST");

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed. Only POST is allowed.']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['titre']) || empty($data['artist']) || empty($data['album'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Les champs titre, artiste et album sont requis.']);
        return;
    }

    try {
        // Récupération et création de l'artiste si nécessaire
        $artistesModel = Artistes::getInstance();
        $artiste = $artistesModel->findOneBy(['nom' => $data['artist']]);
        if (!$artiste) {
            $artistesModel->create(['nom' => $data['artist']]);
            $artiste = $artistesModel->findOneBy(['nom' => $data['artist']]);
        }
        $artisteId = $artiste['id'];

        // Récupération et création de l'album si nécessaire
        $albumsModel = Albums::getInstance();
        $album = $albumsModel->findOneBy(['titre' => $data['album'], 'artiste_id' => $artisteId]);
        if (!$album) {
            $albumsModel->create([
                'titre' => $data['album'],
                'artiste_id' => $artisteId,
                'annee' => $data['annee'] ?? null,
                'genre' => $data['genre'] ?? null
            ]);
            $album = $albumsModel->findOneBy(['titre' => $data['album'], 'artiste_id' => $artisteId]);
        }
        $albumId = $album['id'];

        // Création de la musique
        $musiquesModel = Musiques::getInstance();
        $musiquesModel->create([
            'titre' => $data['titre'],
            'album_id' => $albumId,
            'artiste_id' => $artisteId
        ]);

        // Réponse en cas de succès
        http_response_code(201);
        echo json_encode(['message' => 'Musique ajoutée avec succès']);

    } catch (Exception $e) {
        // Capturer toute erreur interne et renvoyer un message d'erreur
        http_response_code(500);
        echo json_encode(['error' => 'Une erreur est survenue sur le serveur: ' . $e->getMessage()]);
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

        if (isset($data['titre'])) {
            $fieldsToUpdate['titre'] = trim($data['titre']);
        }

        if (empty($fieldsToUpdate)) {
            http_response_code(400);
            echo json_encode(['error' => 'No fields to update']);
            return;
        }

        Musiques::getInstance()->update($id, $fieldsToUpdate);
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
