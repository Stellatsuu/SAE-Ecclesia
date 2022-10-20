<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\Utilisateur;

class UtilisateurRepository {

    public static function construire(array $row): Utilisateur {
        $utilisateur = new Utilisateur(
            $row['idutilisateur'],
            $row['nom'],
            $row['prenom']
        );
        return $utilisateur;
    }

    public static function getUtilisateurs(): array {
        $pdo = DatabaseConnection::getPdo();
        $pdoStatement = $pdo->query("SELECT * FROM utilisateurs");

        $utilisateurs = [];
        foreach ($pdoStatement as $u) {
            $utilisateurs[] = static::construire($u);
        }
        return $utilisateurs;
    }

    public static function getUtilisateurParID($idUtilisateur): ?Utilisateur {
        $pdo = DatabaseConnection::getPdo();
        $sql = "SELECT * FROM utilisateurs WHERE idutilisateur = :idUtilisateur";

        $pdoStatement = $pdo->prepare($sql);

        $values = array(
            "idUtilisateur" => $idUtilisateur
        );

        $pdoStatement->execute($values);

        $row = $pdoStatement->fetch();

        if ($row) {
            return static::construire($row);
        } else {
            return null;
        }
    }

    public static function getQuestionsPoseesParUtilisateur($idUtilisateur): array {
        $pdo = DatabaseConnection::getPdo();
        $sql = "SELECT * FROM questions WHERE idutilisateur = :idUtilisateur";

        $pdoStatement = $pdo->prepare($sql);

        $values = array(
            "idUtilisateur" => $idUtilisateur
        );

        $pdoStatement->execute($values);

        $questions = [];
        foreach ($pdoStatement as $q) {
            $questions[] = QuestionRepository::construire($q);
        }
        return $questions;
    }

    public static function sauvegarder(Utilisateur $utilisateur): void {
        $pdo = DatabaseConnection::getPdo();
        $sql = "INSERT INTO utilisateurs (nom, prenom) VALUES (:nom, :prenom)";

        $pdoStatement = $pdo->prepare($sql);

        $values = array(
            "nom" => $utilisateur->getNom(),
            "prenom" => $utilisateur->getPrenom()
        );

        $pdoStatement->execute($values);
    }

}