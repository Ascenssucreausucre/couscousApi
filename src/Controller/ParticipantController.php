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
            $data = json_decode(file_get_contents('php://input'), true);

            $name = $data['name'] ?? null;
            $imageUrl = $data['image_url'] ?? null;

            if (!$name || !$tournamentId) {
                return $this->json(['error' => 'Le nom et l\'ID du tournoi sont requis.']);
            }

            if (!TournamentService::getInstance()->isTournamentOpen($tournamentId)) {
                return $this->json(['error' => 'Le tournois n\'accepte plus les participants.']);
            }
            // if (!Participant::getInstance()->isTournamentOpen($tournamentId)) {
            //     return $this->json(['error' => 'Ce participant a déjà été ajouté']);
            // }

            try {
                $participantId = Participant::getInstance()->create([
                    'name' => htmlspecialchars($name),
                    'image_url' => htmlspecialchars($imageUrl),
                    'tournament_id' => (int)$tournamentId,
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
    public function getParticipants(int|string $tournamentId)
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
