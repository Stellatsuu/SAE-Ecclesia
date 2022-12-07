<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\AbstractDataObject;
use App\SAE\Model\DataObject\Utilisateur;

class VotantRepository
{
    public function deleteVotantsParQuestion(int $idQuestion): void
    {
        $pdo = DatabaseConnection::getPdo();

        $sql = "DELETE FROM votant WHERE id_question = :id_question";
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'id_question' => $idQuestion
        ];

        $pdoStatement->execute($values);
    }

    public function insert(int $idQuestion, int $idUtilisateur): void
    {
        $pdo = DatabaseConnection::getPdo();

        $sql = "INSERT INTO votant (id_question, id_votant) VALUES (:id_question, :id_votant)";
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'id_question' => $idQuestion,
            'id_votant' => $idUtilisateur
        ];

        $pdoStatement->execute($values);
    }

    public function selectVotantsParQuestion(int $idQuestion): array
    {
        $pdo = DatabaseConnection::getPdo();

        $sql = "SELECT id_votant FROM votant WHERE id_question = :id_question";
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

    public function existeVotant(int $idQuestion, int $idUtilisateur): bool
    {
        $sql = "SELECT COUNT(*) AS est_votant FROM votant WHERE id_question = :idQuestion AND id_votant = :idUtilisateur";
        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute([
            "idQuestion" => $idQuestion,
            "idUtilisateur" => $idUtilisateur
        ]);

        return $pdo->fetch()['est_votant'] > 0;
    }
}
