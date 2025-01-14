<?php

declare(strict_types=1);
/*
-------------------------------------------------------------------------------
les routes
-------------------------------------------------------------------------------
*/

return [
    ['GET', '/tournaments', 'tournament@getPublicTournaments'],

    ['POST', '/user/register', 'user@register'],
    ['POST', '/user/login', 'user@login'],
    ['POST', '/tournament/create', 'tournament@createTournament'],
    ['POST', '/tournament{tournament_id:\d+}/add', 'participant@addParticipant'],

    ['GET', '/user/profile/{user_id:\d+}', 'user@getUserProfile'],
    ['GET', '/tournament/{tournament_id:\d+}/specs', 'tournament@getTournament'],
    ['GET', '/tournament/{participant_id:\d+}', 'participant@getParticipants'],
    ['GET', '/users', 'user@index'],

];
