<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Musiques;
use App\Model\Albums;
use App\Model\Artistes;

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

    // Vérifier que tous les champs nécessaires sont présents
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
            // Création de l'artiste si il n'existe pas
            $artistesModel->create(['nom' => $data['artist']]);
            $artiste = $artistesModel->findOneBy(['nom' => $data['artist']]);
        }
        $artisteId = $artiste['id'];

        // Récupération et création de l'album si nécessaire
        $albumsModel = Albums::getInstance();
        $album = $albumsModel->findOneBy(['titre' => $data['album'], 'artiste_id' => $artisteId]);
        if (!$album) {
            // Création de l'album si il n'existe pas
            $albumsModel->create([
                'titre' => $data['album'],
                'artiste_id' => $artisteId,
                'annee' => $data['annee'] ?? null,  // Si l'année est absente, on met null
                'genre' => $data['genre'] ?? null   // Si le genre est absent, on met null
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
    // En-têtes CORS
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");

    // Vérification de la méthode HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'PATCH') {
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed. Only PATCH is allowed.']);
        return;
    }

    // Récupération de l'ID de la musique à mettre à jour
    $id = (int)$id;
    $musique = Musiques::getInstance()->find($id);

    // Vérification si la musique existe
    if (!$musique) {
        http_response_code(404);
        echo json_encode(['error' => 'Musique not found']);
        return;
    }

    // Récupération des données envoyées par le front-end
    $data = json_decode(file_get_contents('php://input'), true);
    $fieldsToUpdate = [];

    // Mise à jour du titre de la musique si nécessaire
    if (isset($data['titre'])) {
        $fieldsToUpdate['titre'] = trim($data['titre']);
    }

    // Vérification du nouvel artiste et de l'album
    if (isset($data['artist'])) {
        $artistesModel = Artistes::getInstance();
        // Recherche de l'artiste par son nom
        $artiste = $artistesModel->findOneBy(['nom' => $data['artist']]);
        
        if (!$artiste) {
            // Si l'artiste n'existe pas, création de l'artiste
            $artistesModel->create(['nom' => $data['artist']]);
            // Recherche de l'artiste après sa création
            $artiste = $artistesModel->findOneBy(['nom' => $data['artist']]);
        }
        // Ajout de l'ID de l'artiste à mettre à jour
        $fieldsToUpdate['artiste_id'] = $artiste['id'];
    }

    if (isset($data['album'])) {
        $albumsModel = Albums::getInstance();
        // Recherche de l'album par son titre et par l'artiste
        $album = $albumsModel->findOneBy(['titre' => $data['album'], 'artiste_id' => $fieldsToUpdate['artiste_id'] ?? $musique['artiste_id']]);
        
        if (!$album) {
            // Si l'album n'existe pas, création de l'album avec des valeurs par défaut (null)
            $albumsModel->create([
                'titre' => $data['album'],
                'artiste_id' => $fieldsToUpdate['artiste_id'] ?? $musique['artiste_id'],
                'annee' => null,  // Valeur par défaut
                'genre' => null   // Valeur par défaut
            ]);
            // Recherche de l'album après sa création
            $album = $albumsModel->findOneBy(['titre' => $data['album'], 'artiste_id' => $fieldsToUpdate['artiste_id'] ?? $musique['artiste_id']]);
        }
        // Ajout de l'ID de l'album à mettre à jour
        $fieldsToUpdate['album_id'] = $album['id'];
    }

    // Si aucun champ à mettre à jour, renvoyer une erreur
    if (empty($fieldsToUpdate)) {
        http_response_code(400);
        echo json_encode(['error' => 'No fields to update']);
        return;
    }

    // Mise à jour de la musique avec les nouveaux champs
    Musiques::getInstance()->update($id, $fieldsToUpdate);

    // Réponse en cas de succès
    http_response_code(200);
    echo json_encode(['message' => 'Musique updated successfully']);
}


    // Supprimer une musique
    public function delete(int|string $id)
{
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

    // Suppression de la musique
    $deleteSuccess = Musiques::getInstance()->delete($id);
    
    if ($deleteSuccess) {
        http_response_code(200);
        echo json_encode(['message' => 'Musique deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Une erreur est survenue lors de la suppression']);
    }
}

}
