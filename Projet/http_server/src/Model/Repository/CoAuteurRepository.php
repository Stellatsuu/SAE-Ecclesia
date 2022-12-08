<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\AbstractDataObject;
use App\SAE\Model\DataObject\Utilisateur;

class CoAuteurRepository
{
    public function existsForQuestion(int $idQuestion, int $idUtilisateur): bool
    {
        $sql = <<<SQL
        SELECT COUNT(*) AS est_coauteur 
            FROM co_auteur 
            WHERE id_paragraphe IN (SELECT id_paragraphe
                FROM paragraphe pa
                    JOIN proposition pr
                    ON pr.id_proposition = pa.id_proposition
                    JOIN question q
                    ON q.id_question = pr.id_question
                WHERE q.id_question = :idQuestion)
                AND id_utilisateur = :idUtilisateur;
        SQL;

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute([
            "idQuestion" => $idQuestion,
            "idUtilisateur" => $idUtilisateur
        ]);

        return $pdo->fetch()['est_coauteur'] > 0;
    }

    public function existsForParagraphe(int $idParagraphe, int $idUtilisateur): bool
    {
        $sql = <<<SQL
        SELECT COUNT(*) AS est_coauteur 
            FROM co_auteur
            WHERE id_paragraphe = :idParagraphe
            AND id_utilisateur = :idUtilisateur;
        SQL;

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute([
            "idParagraphe" => $idParagraphe,
            "idUtilisateur" => $idUtilisateur
        ]);

        return $pdo->fetch()['est_coauteur'] > 0;
    }

    public function deleteAllByProposition($idProposition)
    {
        $sql = <<<SQL
        DELETE FROM co_auteur
            WHERE id_paragraphe IN (SELECT id_paragraphe
                FROM paragraphe
                WHERE id_proposition = :id_proposition);
        SQL;

        $values = [
            "id_proposition" => $idProposition
        ];

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute($values);
    }

    public function insert($idParagraphe, $idUtilisateur)
    {
        $sql = <<<SQL
        INSERT INTO co_auteur (id_paragraphe, id_utilisateur) 
            VALUES (:id_paragraphe, :id_utilisateur);
        SQL;

        $values = [
            "id_paragraphe" => $idParagraphe,
            "id_utilisateur" => $idUtilisateur
        ];

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute($values);
    }

    public function insertGlobal($idProposition, $idUtilisateur)
    {
        $sql = <<<SQL
        INSERT INTO co_auteur (id_paragraphe, id_utilisateur) 
            SELECT p.id_paragraphe, :id_utilisateur 
                FROM paragraphe p 
                WHERE p.id_proposition = :id_proposition;
        SQL;

        $values = [
            "id_proposition" => $idProposition,
            "id_utilisateur" => $idUtilisateur
        ];

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute($values);
    }

    public function selectAllByProposition($idProposition)
    {
        $sql = <<<SQL
        SELECT DISTINCT id_utilisateur 
            FROM co_auteur ca 
                JOIN paragraphe p 
                ON p.id_paragraphe = ca.id_paragraphe 
            WHERE p.id_proposition = :id_proposition
        SQL;

        $values = [
            "id_proposition" => $idProposition
        ];

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute($values);

        $resultat = [];
        foreach ($pdo as $row) {
            $resultat[] = (new UtilisateurRepository)->select($row['id_utilisateur']);
        }
        return $resultat;
    }

    public function selectAllByParagraphe(int $idParagraphe): array
    {
        $sql = <<<SQL
        SELECT * 
            FROM co_auteur 
            WHERE id_paragraphe = :idParagraphe
        SQL;

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute(["idParagraphe" => $idParagraphe]);

        $coAuteurs = [];
        foreach ($pdo->fetchAll() as $auteur) {
            $coAuteurs[] = Utilisateur::castIfNotNull((new UtilisateurRepository())->select($auteur['id_utilisateur']));
        }

        return $coAuteurs;
    }
}
