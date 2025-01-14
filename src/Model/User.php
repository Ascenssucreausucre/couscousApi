<?php

declare(strict_types=1);

namespace App\Model;

class User extends Model
{
    protected $tableName = APP_TABLE_PREFIX . 'users';

    use TraitInstance;
}
