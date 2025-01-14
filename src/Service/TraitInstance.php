<?php

declare(strict_types=1);

namespace App\Service;

trait TraitInstance
{
    protected static $instance;

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
