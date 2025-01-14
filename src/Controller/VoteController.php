<?php

declare(strict_types=1); // strict mode

namespace App\Controller;

use App\Model\Vote;

class VoteController extends Controller
{
    /**
     * Page d'accueil pour lister tous les étudiants.
     * @route [get] /étudiants
     *
     */
    public function index()
    {
        $votes = Vote::getInstance()->findAll();

        echo json_encode($votes);
    }
}
