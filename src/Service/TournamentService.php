<?php

declare(strict_types=1); // strict mode

namespace App\Service;

use App\Model\Tournament;
use App\Model\Participant;

class TournamentService
{
    public function isTournamentOpen($tournamentId) {
        $tournament = Tournament::getInstance()->findOneBy(['tournament_id'=>$tournamentId]);
        return $tournament && $tournament['is_open'];
    }

    public function participantExists(int $tournamentId, string $name){
        $tournament = Tournament::getInstance()->findOneBy(['tournament_id'=>$tournamentId]); // à terminer
    }

    public function hasSpaceForMoreParticipants($tournamentId) {
        $participantCount = Participant::countByTournamentId($tournamentId);
        $tournament = Tournament::findById($tournamentId);
        return $participantCount < $tournament['max_participants'];
    }
    use TraitInstance;
}
