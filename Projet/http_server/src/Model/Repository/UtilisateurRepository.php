<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\Utilisateur;

class UtilisateurRepository extends AbstractRepository {

    protected function getNomTable(): string
    {
        return 'utilisateur';
    }

    protected function getNomClePrimaire(): string
    {
        return 'id_utilisateur';
    }

    protected function getNomsColonnes(): array
    {
        return [
            'nom_utilisateur',
            'prenom_utilisateur'
        ];
    }

    public function construire(array $row): Utilisateur {
        $utilisateur = new Utilisateur(
            $row['id_utilisateur'],
            $row['nom_utilisateur'],
            $row['prenom_utilisateur']
        );
        return $utilisateur;
    }

    //vérification que l'utilisateur est soit l'organisateur, soit un rédacteur, soit un co-auteur, soit un votant de la question
    public function estLieAQuestion(int $idUtilisateur, int $idQuestion): bool {
        $sql = <<<SQL
        SELECT utilisateur_est_lie_a_question(:idUtilisateur, :idQuestion) AS est_lie_a_question;
        SQL;

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute([
            "idUtilisateur" => $idUtilisateur,
            "idQuestion" => $idQuestion
        ]);

        return $pdo->fetch()['est_lie_a_question'] > 0;
    }
} 