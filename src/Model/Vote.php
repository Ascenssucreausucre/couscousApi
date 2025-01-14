<?php

declare(strict_types=1);

namespace App\Model;

class Vote extends Model
{
    protected $tableName = APP_TABLE_PREFIX . 'votes';

    use TraitInstance;
}
