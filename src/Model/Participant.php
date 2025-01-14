<?php

declare(strict_types=1);

namespace App\Model;

class Participant extends Model
{
    protected $tableName = APP_TABLE_PREFIX . 'participants';

    use TraitInstance;
}
