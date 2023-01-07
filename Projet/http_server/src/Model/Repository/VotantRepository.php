<?php

namespace App\SAE\Model\Repository;

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

    public function insert(int $idQuestion, string $username): void
    {
        $sql = <<<SQL
        INSERT INTO votant (id_question, username_votant)
            VALUES (:id_question, :username_votant)
        SQL;

        $pdo = DatabaseConnection::getPdo();
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'id_question' => $idQuestion,
            'username_votant' => $username
        ];

        $pdoStatement->execute($values);
    }

    public function selectAllByQuestion(int $idQuestion): array
    {
        $sql = <<<SQL
        SELECT username_votant
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
            $votants[] = (new UtilisateurRepository)->select($row['username_votant']);
        }
        return $votants;
    }

    public function existsForQuestion(int $idQuestion, string $username): bool
    {
        $sql = <<<SQL
        SELECT COUNT(*) AS est_votant
            FROM votant
            WHERE id_question = :id_question
            AND username_votant = :username_votant
        SQL;

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute([
            "id_question" => $idQuestion,
            "username_votant" => $username
        ]);

        return $pdo->fetch()['est_votant'] > 0;
    }
}
