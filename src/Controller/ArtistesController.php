<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Artistes;

class ArtistesController extends Controller
{
    // Lister tous les artistes
    public function index()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type");
        header("Content-Type: application/json");

        $artistes = Artistes::getInstance()->findAll();
        echo json_encode($artistes);
    }

    // Afficher un artiste spécifique
    public function show($id)
    {
        $id = (int)$id;

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type");
        header("Content-Type: application/json");

        $artiste = Artistes::getInstance()->find($id);

        if ($artiste) {
            echo json_encode($artiste);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Artiste not found']);
        }
    }

    // Créer un nouvel artiste
    public function create()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type");
        header("Content-Type: application/json");

        if ($this->isPostMethod()) {
            $data = json_decode(file_get_contents('php://input'), true);

            if (isset($data['nom'])) {
                $artiste = Artistes::getInstance()->create(['nom' => $data['nom']]);
                http_response_code(201);
                echo json_encode($artiste);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid input data']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
    }

    // Mettre à jour un artiste
    public function update(int|string $id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'PATCH') {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed. Only PATCH is allowed.']);
            return;
        }

        $id = (int)$id;
        $artiste = Artiste::getInstance()->find($id);

        if (!$artiste) {
            http_response_code(404);
            echo json_encode(['error' => 'Artiste not found']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $fieldsToUpdate = [];

        if (isset($data['nom'])) {
            $fieldsToUpdate['nom'] = trim($data['nom']);
        }

        if (empty($fieldsToUpdate)) {
            http_response_code(400);
            echo json_encode(['error' => 'No fields to update']);
            return;
        }

        Artiste::getInstance()->update($id, $fieldsToUpdate);
        http_response_code(200);
        echo json_encode(['message' => 'Artiste updated successfully']);
    }

    // Supprimer un artiste
    public function delete(int|string $id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed. Only DELETE is allowed.']);
            return;
        }

        $id = (int)$id;
        $artiste = Artiste::getInstance()->find($id);

        if (!$artiste) {
            http_response_code(404);
            echo json_encode(['error' => 'Artiste not found']);
            return;
        }

        Artiste::getInstance()->delete($id);
        http_response_code(200);
        echo json_encode(['message' => 'Artiste deleted successfully']);
    }
}
