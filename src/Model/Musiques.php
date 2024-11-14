<?php

declare(strict_types=1);

namespace App\Model;

class Musiques extends Model
{
    use TraitInstance;

    protected $tableName = APP_TABLE_PREFIX . 'Musiques';
}
