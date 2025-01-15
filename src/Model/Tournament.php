<?php

declare(strict_types=1);

namespace App\Model;

class Tournament extends Model
{
    protected $tableName = APP_TABLE_PREFIX . 'tournaments';

    public function findTournamentsByPage(int $page, int $perPage): array
    {
        // Validation stricte des paramètres
        if ($page < 1 || $perPage < 1) {
            throw new \InvalidArgumentException('Page and perPage must be greater than 0.');
        }
    
        // Calculer l'offset
        $offset = ($page - 1) * $perPage;
    
        // Construire la requête avec les valeurs intégrées directement
        $sql = "SELECT * FROM `{$this->tableName}` LIMIT $perPage OFFSET $offset";
    
        // Exécuter la requête
        return $this->query($sql)->fetchAll();
    }
    

    public function countTournaments(): int
    {
        $sql = "SELECT COUNT(*) as count FROM `{$this->tableName}` WHERE is_public = 1";
        $sth = $this->query($sql);
        $result = $sth->fetch();
        return $result['count'] ?? 0;
    }



    use TraitInstance;
}
