<?php

declare(strict_types=1);
/*
-------------------------------------------------------------------------------
les routes
-------------------------------------------------------------------------------
*/

return [
    // Routes pour les Artistes
    ['GET', '/api/v1/artistes', 'artistes@index'],               // Lister tous les artistes
    ['GET', '/api/v1/artistes/{id:\d+}', 'artistes@show'],        // Afficher un artiste spécifique
    ['POST', '/api/v1/artistes', 'artistes@create'],              // Créer un nouvel artiste
    ['PATCH', '/api/v1/artistes/{id:\d+}', 'artistes@update'],    // Mettre à jour un artiste
    ['DELETE', '/api/v1/artistes/{id:\d+}', 'artistes@delete'],   // Supprimer un artiste

    // Routes pour les Albums
    ['GET', '/api/v1/albums', 'albums@index'],                   // Lister tous les albums
    ['GET', '/api/v1/albums/{id:\d+}', 'albums@show'],            // Afficher un album spécifique
    ['POST', '/api/v1/albums', 'albums@create'],                  // Créer un nouvel album
    ['PATCH', '/api/v1/albums/{id:\d+}', 'albums@update'],        // Mettre à jour un album
    ['DELETE', '/api/v1/albums/{id:\d+}', 'albums@delete'],       // Supprimer un album

    // Routes pour les Musiques
    ['GET', '/api/v1/musiques', 'musiques@index'],               // Lister toutes les musiques
    ['GET', '/api/v1/musiques/{id:\d+}', 'musiques@show'],        // Afficher une musique spécifique
    ['POST', '/api/v1/musiques', 'musiques@create'],              // Créer une nouvelle musique
    ['PATCH', '/api/v1/musiques/{id:\d+}', 'musiques@update'],    // Mettre à jour une musique
    ['DELETE', '/api/v1/musiques/{id:\d+}', 'musiques@delete'],   // Supprimer une musique
];
