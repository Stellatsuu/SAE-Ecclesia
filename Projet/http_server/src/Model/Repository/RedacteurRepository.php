<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\AbstractDataObject;
use App\SAE\Model\DataObject\Utilisateur;

class RedacteurRepository
{
    public function deleteAllByQuestion(int $idQuestion): void
    {
        $sql = <<<SQL
        DELETE FROM redacteur
            WHERE id_question = :id_question
        SQL;

        $pdo = DatabaseConnection::getPdo();
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'id_question' => $idQuestion
        ];

        $pdoStatement->execute($values);
    }

    public function insert(int $idQuestion, string $username): void
    {
        $sql = <<<SQL
        INSERT INTO redacteur (id_question, username_redacteur)
            VALUES (:id_question, :username_redacteur)
        SQL;

        $pdo = DatabaseConnection::getPdo();
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'id_question' => $idQuestion,
            'username_redacteur' => $username
        ];

        $pdoStatement->execute($values);
    }

    public function selectAllByQuestion(int $idQuestion): array
    {
        $sql = <<<SQL
        SELECT username_redacteur
            FROM redacteur
            WHERE id_question = :id_question
        SQL;

        $pdo = DatabaseConnection::getPdo();
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'id_question' => $idQuestion
        ];

        $pdoStatement->execute($values);

        $redacteurs = [];
        foreach ($pdoStatement as $row) {
            $redacteurs[] = (new UtilisateurRepository)->select($row['username_redacteur']);
        }
        return $redacteurs;
    }

    public function existsForQuestion(int $idQuestion, string $username): bool
    {
        $sql = <<<SQL
        SELECT COUNT(*) AS est_redacteur
            FROM redacteur
            WHERE id_question = :idQuestion
            AND username_redacteur = :username_redacteur
        SQL;
        
        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute([
            "idQuestion" => $idQuestion,
            "username_redacteur" => $username
        ]);

        return $pdo->fetch()['est_redacteur'] > 0;
    }
}
