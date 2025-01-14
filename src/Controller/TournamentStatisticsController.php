<?php

declare(strict_types=1); // strict mode

namespace App\Controller;

use App\Model\TournamentStatistics;

class TournamentStatisticsController extends Controller
{
    /**
     * Page d'accueil pour lister tous les étudiants.
     * @route [get] /étudiants
     *
     */
    public function index()
    {
        $globalStatistics = GlobalStatistics::getInstance()->findAll();

        echo json_encode($globalStatistics);
    }
}
