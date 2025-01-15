<?php

declare(strict_types=1);
/*
-------------------------------------------------------------------------------
les routes
-------------------------------------------------------------------------------
*/

return [
    ['GET', '/tournaments', 'tournament@getAllTournaments'],
    ['GET', '/tournaments/page/{page:\d+}/count/{per_page:\d+}', 'tournament@getTournamentsByPage'],
    ['GET', '/user/profile/{user_id:\d+}', 'user@getUserProfile'],
    ['GET', '/tournament/{tournament_id:\d+}/specs', 'tournament@getTournament'],
    ['GET', '/tournament/{tournament_id:\d+}', 'participant@getAllParticipants'],
    ['GET', '/users', 'user@index'],

    ['POST', '/user/register', 'user@register'],
    ['POST', '/user/login', 'user@login'],
    ['POST', '/tournament/create', 'tournament@createTournament'],
    ['POST', '/tournament{tournament_id:\d+}/add', 'participant@addParticipant'],
];
