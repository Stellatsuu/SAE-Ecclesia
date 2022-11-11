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

    public static function getDemandesFaitesParUtilisateur($idUtilisateur): array {
        $pdo = DatabaseConnection::getPdo();
        $sql = "SELECT * FROM question WHERE id_utilisateur = :id_utilisateur";

        $pdoStatement = $pdo->prepare($sql);

        $values = array(
            "id_utilisateur" => $idUtilisateur
        );

        $pdoStatement->execute($values);

        $demandes = [];
        foreach ($pdoStatement as $q) {
            $demandes[] = (new DemandeQuestionRepository)->construire($q);
        }
        return $demandes;
    }
}