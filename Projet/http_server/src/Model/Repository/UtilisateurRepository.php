<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\AbstractDataObject;
use App\SAE\Model\DataObject\Utilisateur;

class UtilisateurRepository extends AbstractRepository
{

    protected function getNomTable(): string
    {
        return 'utilisateur';
    }

    protected function getNomClePrimaire(): string
    {
        return 'username_utilisateur';
    }

    protected function getNomsColonnes(): array
    {
        return [
            'username_utilisateur',
            'nom_utilisateur',
            'prenom_utilisateur',
            'email_utilisateur',
            'photo_profil',
            'mdp_hashed'
        ];
    }

    public function construire(array $row): Utilisateur
    {
        return new Utilisateur(
            $row['username_utilisateur'],
            $row['nom_utilisateur'],
            $row['prenom_utilisateur'],
            $row['email_utilisateur'],
            $row['mdp_hashed']
        );
    }

    //vérification que l'utilisateur est soit l'organisateur, soit un rédacteur, soit un co-auteur, soit un votant de la question
    public function estLieAQuestion(string $username, int $idQuestion): bool
    {
        $sql = <<<SQL
        SELECT utilisateur_est_lie_a_question(:username_utilisateur, :id_question) AS est_lie_a_question;
        SQL;

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute([
            "username_utilisateur" => $username,
            "id_question" => $idQuestion
        ]);

        return $pdo->fetch()['est_lie_a_question'] > 0;
    }

    public function insert(AbstractDataObject $object): void
    {
        $sql = <<<SQL
        INSERT INTO utilisateur (username_utilisateur, nom_utilisateur, prenom_utilisateur, email_utilisateur, photo_profil, mdp_hashed)
        VALUES (:username_utilisateur, :nom_utilisateur, :prenom_utilisateur, :email_utilisateur, decode(:photo_profil, 'base64'), :mdp_hashed);
        SQL;

        $values = $object->formatTableau();
        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute($values);
    }

    public function select(...$params): ?AbstractDataObject
    {
        if (count($params) != 1) {
            throw new \Exception("Un seul paramètre est requis");
        }

        $sql = <<<SQL
        SELECT username_utilisateur, nom_utilisateur, prenom_utilisateur, email_utilisateur, mdp_hashed
        FROM utilisateur
        WHERE username_utilisateur = :username_utilisateur;
        SQL;

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $values = [
            'username_utilisateur' => $params[0]
        ];
        $pdo->execute($values);

        $row = $pdo->fetch();
        if ($row === false) {
            return null;
        }

        return $this->construire($row);
    }

    public function selectAll(): array
    {
        $sql = <<<SQL
        SELECT username_utilisateur, nom_utilisateur, prenom_utilisateur, email_utilisateur, mdp_hashed
        FROM utilisateur;
        SQL;

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute();

        $rows = $pdo->fetchAll();
        if ($rows === false) {
            return [];
        }

        $utilisateurs = [];
        foreach ($rows as $row) {
            $utilisateurs[] = $this->construire($row);
        }

        return $utilisateurs;
    }

    public function selectPhotoProfil($username)
    {
        $sql = <<<SQL
        SELECT encode(photo_profil, 'base64') AS photo_profil
        FROM utilisateur
        WHERE username_utilisateur = :username
        SQL;

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute([
            'username' => $username
        ]);

        $row = $pdo->fetch();
        if ($row === false) {
            return null;
        }

        return $row['photo_profil'];
    }

    public function update(AbstractDataObject $object): void
    {
        $sql = <<<SQL
        UPDATE utilisateur
        SET nom_utilisateur = :nom_utilisateur, prenom_utilisateur = :prenom_utilisateur, email_utilisateur = :email_utilisateur, photo_profil = decode(:photo_profil, 'base64'), mdp_hashed = :mdp_hashed
        WHERE username_utilisateur = :username_utilisateur;
        SQL;

        $values = $object->formatTableau();
        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute($values);
    }
}
