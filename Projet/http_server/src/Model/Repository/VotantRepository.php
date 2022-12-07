<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\AbstractDataObject;
use App\SAE\Model\DataObject\Utilisateur;

class VotantRepository
{
    public function deleteAllByQuestion(int $idQuestion): void
    {
        $sql = <<<SQL
        DELETE FROM votant
            WHERE id_question = :id_question
        SQL;

        $pdo = DatabaseConnection::getPdo();
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'id_question' => $idQuestion
        ];

        $pdoStatement->execute($values);
    }

    public function insert(int $idQuestion, int $idUtilisateur): void
    {
        $sql = <<<SQL
        INSERT INTO votant (id_question, id_votant)
            VALUES (:id_question, :id_votant)
        SQL;

        $pdo = DatabaseConnection::getPdo();
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'id_question' => $idQuestion,
            'id_votant' => $idUtilisateur
        ];

        $pdoStatement->execute($values);
    }

    public function selectAllByQuestion(int $idQuestion): array
    {
        $sql = <<<SQL
        SELECT id_votant
            FROM votant
            WHERE id_question = :id_question
        SQL;

        $pdo = DatabaseConnection::getPdo();
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'id_question' => $idQuestion
        ];

        $pdoStatement->execute($values);

        $votants = [];
        foreach ($pdoStatement as $row) {
            $votants[] = (new UtilisateurRepository)->select($row['id_votant']);
        }
        return $votants;
    }

    public function existsOnQuestion(int $idQuestion, int $idUtilisateur): bool
    {
        $sql = <<<SQL
        SELECT COUNT(*) AS est_votant
            FROM votant
            WHERE id_question = :id_question
            AND id_votant = :id_votant
        SQL;

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute([
            "idQuestion" => $idQuestion,
            "idUtilisateur" => $idUtilisateur
        ]);

        return $pdo->fetch()['est_votant'] > 0;
    }
}
