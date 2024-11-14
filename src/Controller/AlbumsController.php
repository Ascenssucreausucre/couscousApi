<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Albums;

class AlbumsController extends Controller
{
    // Lister tous les albums
    public function index()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type");
        header("Content-Type: application/json");

        $albums = Albums::getInstance()->findAll();
        echo json_encode($albums);
    }

    // Afficher un album spécifique
    public function show($id)
    {
        $id = (int)$id;

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type");
        header("Content-Type: application/json");

        $album = Albums::getInstance()->find($id);

        if ($album) {
            echo json_encode($album);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Album not found']);
        }
    }

    // Créer un nouvel album
    public function create()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type");
        header("Content-Type: application/json");

        if ($this->isPostMethod()) {
            $data = json_decode(file_get_contents('php://input'), true);

            if (isset($data['titre']) && isset($data['artiste_id'])) {
                $album = Albums::getInstance()->create([
                    'titre' => $data['titre'],
                    'artiste_id' => $data['artiste_id'],
                ]);
                http_response_code(201);
                echo json_encode($album);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid input data']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
    }

    // Mettre à jour un album
    public function update(int|string $id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'PATCH') {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed. Only PATCH is allowed.']);
            return;
        }

        $id = (int)$id;
        $album = Albums::getInstance()->find($id);

        if (!$album) {
            http_response_code(404);
            echo json_encode(['error' => 'Album not found']);
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

        Albums::getInstance()->update($id, $fieldsToUpdate);
        http_response_code(200);
        echo json_encode(['message' => 'Album updated successfully']);
    }

    // Supprimer un album
    public function delete(int|string $id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed. Only DELETE is allowed.']);
            return;
        }

        $id = (int)$id;
        $album = Albums::getInstance()->find($id);

        if (!$album) {
            http_response_code(404);
            echo json_encode(['error' => 'Album not found']);
            return;
        }

        Album::getInstance()->delete($id);
        http_response_code(200);
        echo json_encode(['message' => 'Album deleted successfully']);
    }
}
