<?php

declare(strict_types=1);

namespace App\Model;

class Artistes extends Model
{
    use TraitInstance;

    protected $tableName = APP_TABLE_PREFIX . 'Artistes';
}
