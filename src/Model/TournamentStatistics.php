<?php

declare(strict_types=1);

namespace App\Model;

class TournamentStatistics extends Model
{
    protected $tableName = APP_TABLE_PREFIX . 'tournament_statistics';

    use TraitInstance;
}
