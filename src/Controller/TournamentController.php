<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Tournament;
use App\Model\Participant;
use App\Model\GlobalStatistics;

class TournamentController extends Controller
{
    /**
     * Créer un tournoi.
     */
    public function createTournament()
    {
        if ($this->isPostMethod()) {
            $data = json_decode(file_get_contents('php://input'), true);

            $name = $data['name'] ?? null;
            $description = $data['description'] ?? null;
            $createdBy = $data['user_id'] ?? null;

            if (!$name || !$createdBy) {
                return $this->json(['error' => 'Le nom et l\'utilisateur créateur sont requis.']);
            }

            try {
                $tournamentId = Tournament::getInstance()->create([
                    'name' => htmlspecialchars($name),
                    'description' => htmlspecialchars($description),
                    'created_by' => $createdBy,
                ]);

                return $this->json(['success' => true, 'tournament_id' => $tournamentId]);
            } catch (\Exception $e) {
                return $this->json(['error' => $e->getMessage()]);
            }
        }

        return $this->json(['error' => 'Requête non autorisée.']);
    }

    /**
     * Obtenir tous les tournois publics.
     */
    public function getPublicTournaments()
    {
        try {
            $tournaments = Tournament::getInstance()->findAllBy(['is_public' => true]);
            return $this->json(['success' => true, 'data' => $tournaments]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Obtenir un tournoi par son ID.
     */
    public function getTournament(int|string $tournamentId)
    {
        $tournamentId = (int)$tournamentId;
        try {
            $tournament = Tournament::getInstance()->findOneBy(['tournament_id'=>$tournamentId]);

            if (!$tournament) {
                return $this->json(['error' => 'Tournoi introuvable.']);
            }

            return $this->json(['success' => true, 'data' => $tournament]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Supprimer un tournoi.
     */
    public function deleteTournament(int $tournamentId)
    {
        $createdBy = $_SESSION['user']['user_id'] ?? null;

        if (!$createdBy) {
            return $this->json(['error' => 'Non autorisé.']);
        }

        try {
            $tournament = Tournament::getInstance()->find($tournamentId);

            if (!$tournament || $tournament['created_by'] != $createdBy) {
                return $this->json(['error' => 'Action non autorisée ou tournoi introuvable.']);
            }

            Tournament::getInstance()->delete($tournamentId);
            return $this->json(['success' => true]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()]);
        }
    }
}
