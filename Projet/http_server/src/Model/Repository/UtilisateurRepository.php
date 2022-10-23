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
        return 'idutilisateur';
    }

    protected function getNomsColonnes(): array
    {
        return [
            'nom',
            'prenom'
        ];
    }

    public function construire(array $row): Utilisateur {
        $utilisateur = new Utilisateur(
            $row['idutilisateur'],
            $row['nom'],
            $row['prenom']
        );
        return $utilisateur;
    }

    public static function getDemandesFaitesParUtilisateur($idUtilisateur): array {
        $pdo = DatabaseConnection::getPdo();
        $sql = "SELECT * FROM question WHERE idutilisateur = :idutilisateur";

        $pdoStatement = $pdo->prepare($sql);

        $values = array(
            "idutilisateur" => $idUtilisateur
        );

        $pdoStatement->execute($values);

        $demandes = [];
        foreach ($pdoStatement as $q) {
            $demandes[] = (new DemandeQuestionRepository)->construire($q);
        }
        return $demandes;
    }
}