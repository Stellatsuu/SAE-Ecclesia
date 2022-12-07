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
}