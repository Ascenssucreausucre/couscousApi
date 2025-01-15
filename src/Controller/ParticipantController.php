<?php

declare(strict_types=1); // strict mode

namespace App\Controller;

use App\Model\Participant;
use App\Service\TournamentService;

class ParticipantController extends Controller
{
    public function index()
    {
        $participants = Participant::getInstance()->findAll();

        echo json_encode($participants);
    }

    /**
     * Ajouter un participant à un tournoi.
     */
    public function addParticipant($tournamentId)
    {
        $tournamentId = (int)$tournamentId;

        if ($this->isPostMethod()) {
            $name = $_POST['name'] ?? null; // Récupération depuis $_POST
            $uploadedFile = $_FILES['image'] ?? null; // Récupération du fichier depuis $_FILES

            if (!$name || !$tournamentId) {
                return $this->json(['error' => 'Le nom et l\'ID du tournoi sont requis.']);
            }

            if (!TournamentService::getInstance()->isTournamentOpen($tournamentId)) {
                return $this->json(['error' => 'Le tournoi n\'accepte plus les participants.']);
            }

            $currentParticipants = Participant::getInstance()->findAllBy(['tournament_id' => $tournamentId]);

            $alreadyExists = array_filter($currentParticipants, function ($participant) use ($name) {
                return $participant['name'] === $name;
            });

            if (!empty($alreadyExists)) {
                return $this->json(['error' => 'Ce participant a déjà été ajouté à ce tournoi.']);
            }

            // Vérification et traitement du fichier uploadé
            if ($uploadedFile && $uploadedFile['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../uploads/';

                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $maxFileSize = 2 * 1024 * 1024; // 2MB

                // Vérifier le type de fichier
                if (!in_array($uploadedFile['type'], $allowedTypes)) {
                    return $this->json(['error' => 'Le fichier doit être une image (JPEG, PNG ou GIF).']);
                }

                // Vérifier la taille du fichier
                if ($uploadedFile['size'] > $maxFileSize) {
                    return $this->json(['error' => 'L\'image ne doit pas dépasser 2 Mo.']);
                }

                $filename = uniqid('participant_', true) . '.' . pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
                $filePath = $uploadDir . $filename;

                if (!move_uploaded_file($uploadedFile['tmp_name'], $filePath)) {
                    return $this->json(['error' => 'Erreur lors du téléchargement de l\'image.']);
                }

                // Générer l'URL de l'image
                $imageUrl = '/uploads/' . $filename;
            } else {
                return $this->json(['error' => 'Le fichier image est requis ou invalide.']);
            }

            try {
                $participantId = Participant::getInstance()->create([
                    'name' => htmlspecialchars($name),
                    'image_url' => htmlspecialchars($imageUrl),
                    'tournament_id' => $tournamentId,
                ]);

                return $this->json(['success' => true, 'participant_id' => $participantId]);
            } catch (\Exception $e) {
                return $this->json(['error' => $e->getMessage()]);
            }
        }

        return $this->json(['error' => 'Requête non autorisée.']);
    }
    /**
     * Obtenir tous les participants d'un tournoi.
     */
    public function getAllParticipants(int|string $tournamentId)
    {
        $tournamentId = (int)$tournamentId;

        try {
            $participants = Participant::getInstance()->findAllBy(['tournament_id' => $tournamentId]);

            if (!$participants) {
                return $this->json(['error' => 'Aucun participant trouvé.']);
            }

            return $this->json(['success' => true, 'data' => $participants]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Supprimer un participant.
     */
    public function deleteParticipant(int $participantId)
    {
        try {
            Participant::getInstance()->delete($participantId);
            return $this->json(['success' => true]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()]);
        }
    }
}
