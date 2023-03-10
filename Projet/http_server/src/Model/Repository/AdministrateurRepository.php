<?php

namespace App\SAE\Model\Repository;

class AdministrateurRepository
{

    public function existe(string $username) : bool {
        $sql = <<<SQL
        SELECT COUNT(*) AS est_administrateur
        FROM administrateur 
        WHERE username_administrateur = :username
        SQL;

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute([
            "username" => $username
        ]);

        return $pdo->fetch()['est_administrateur'] > 0;
    }

}
