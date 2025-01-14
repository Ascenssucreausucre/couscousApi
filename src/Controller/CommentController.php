<?php

declare(strict_types=1); // strict mode

namespace App\Controller;

use App\Model\Comment;

class CommentController extends Controller
{
    /**
     * Page d'accueil pour lister tous les étudiants.
     * @route [get] /étudiants
     *
     */
    public function index()
    {
        $comments = Comment::getInstance()->findAll();

        echo json_encode($comments);
    }
}
