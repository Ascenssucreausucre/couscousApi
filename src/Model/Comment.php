<?php

declare(strict_types=1);

namespace App\Model;

class Comment extends Model
{
    protected $tableName = APP_TABLE_PREFIX . 'comments';

    use TraitInstance;
}
