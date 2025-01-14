<?php

declare(strict_types=1);

namespace App\Model;

class Tournament extends Model
{
    protected $tableName = APP_TABLE_PREFIX . 'tournaments';

    use TraitInstance;
}
