<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\Utilisateur;

class CoAuteurRepository
{
    public function existsForQuestion(int $idQuestion, string $username): bool
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
                AND username_co_auteur = :username;
        SQL;

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute([
            "idQuestion" => $idQuestion,
            "username" => $username
        ]);

        return $pdo->fetch()['est_coauteur'] > 0;
    }

    public function existsForProposition(int $idProposition, string $username): bool
    {
        $sql = <<<SQL
        SELECT COUNT(*) AS est_coauteur 
            FROM co_auteur ca JOIN paragraphe p
                ON ca.id_paragraphe = p.id_paragraphe
            WHERE p.id_proposition = :id_proposition
            AND ca.username_co_auteur = :username;
        SQL;

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute([
            "id_proposition" => $idProposition,
            "username" => $username
        ]);

        return $pdo->fetch()['est_coauteur'] > 0;
    }

    public function existsForParagraphe(int $idParagraphe, string $username): bool
    {
        $sql = <<<SQL
        SELECT COUNT(*) AS est_coauteur 
            FROM co_auteur
            WHERE id_paragraphe = :idParagraphe
            AND username_co_auteur = :username;
        SQL;

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute([
            "idParagraphe" => $idParagraphe,
            "username" => $username
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

    public function insert(int $idParagraphe, string $username)
    {
        $sql = <<<SQL
        INSERT INTO co_auteur (id_paragraphe, username_co_auteur)
            VALUES (:id_paragraphe, :username_co_auteur)
        SQL;

        $values = [
            "id_paragraphe" => $idParagraphe,
            "username_co_auteur" => $username
        ];

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute($values);
    }

    public function insertGlobal(int $idProposition, string $username)
    {
        $sql = <<<SQL
        INSERT INTO co_auteur (id_paragraphe, username_co_auteur)
            SELECT p.id_paragraphe, :username_co_auteur
                FROM paragraphe p 
                WHERE p.id_proposition = :id_proposition;
        SQL;

        $values = [
            "id_proposition" => $idProposition,
            "username_co_auteur" => $username
        ];

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute($values);
    }

    public function selectAllByProposition($idProposition)
    {
        $sql = <<<SQL
        SELECT DISTINCT username_co_auteur
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
            $resultat[] = (new UtilisateurRepository)->select($row['username_co_auteur']);
        }
        return $resultat;
    }

    public function selectAllByParagraphe(int $idParagraphe): array
    {
        $sql = <<<SQL
        SELECT username_co_auteur
            FROM co_auteur 
            WHERE id_paragraphe = :idParagraphe
        SQL;

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute(["idParagraphe" => $idParagraphe]);

        $coAuteurs = [];
        foreach ($pdo->fetchAll() as $auteur) {
            $coAuteurs[] = Utilisateur::castIfNotNull((new UtilisateurRepository())->select($auteur['username_co_auteur']));
        }

        return $coAuteurs;
    }
}
