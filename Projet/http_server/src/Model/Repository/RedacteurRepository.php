<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\AbstractDataObject;
use App\SAE\Model\DataObject\Utilisateur;

class RedacteurRepository
{
    public function deleteRedacteursParQuestion(int $idQuestion): void
    {
        $pdo = DatabaseConnection::getPdo();

        $sql = "DELETE FROM redacteur WHERE id_question = :id_question";
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'id_question' => $idQuestion
        ];

        $pdoStatement->execute($values);
    }

    public function insert(int $idQuestion, int $idUtilisateur): void
    {
        $pdo = DatabaseConnection::getPdo();

        $sql = "INSERT INTO redacteur (id_question, id_redacteur) VALUES (:id_question, :id_redacteur)";
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'id_question' => $idQuestion,
            'id_redacteur' => $idUtilisateur
        ];

        $pdoStatement->execute($values);
    }

    public function selectRedacteursParQuestion(int $idQuestion): array
    {
        $pdo = DatabaseConnection::getPdo();

        $sql = "SELECT id_redacteur FROM redacteur WHERE id_question = :id_question";
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'id_question' => $idQuestion
        ];

        $pdoStatement->execute($values);

        $redacteurs = [];
        foreach ($pdoStatement as $row) {
            $redacteurs[] = (new UtilisateurRepository)->select($row['id_redacteur']);
        }
        return $redacteurs;
    }

    public function existeRedacteur(int $idQuestion, int $idUtilisateur): bool
    {
        $sql = "SELECT COUNT(*) AS est_redacteur FROM redacteur WHERE id_question = :idQuestion AND id_redacteur = :idUtilisateur";
        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute([
            "idQuestion" => $idQuestion,
            "idUtilisateur" => $idUtilisateur
        ]);

        return $pdo->fetch()['est_redacteur'] > 0;
    }
}
