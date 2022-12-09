<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\AbstractDataObject;
use App\SAE\Model\DataObject\DemandeCoAuteur;

class DemandeCoAuteurRepository extends AbstractRepository
{

    protected function getNomTable(): string
    {
        return "demande_co_auteur";
    }

    protected function getNomClePrimaire(): string
    {
        return "username_demandeur, id_proposition";
    }

    protected function getNomsColonnes(): array
    {
        return [
            "username_demandeur",
            "id_proposition",
            "message"
        ];
    }

    protected function construire(array $objetFormatTableau): DemandeCoAuteur
    {
        return new DemandeCoAuteur(
            $objetFormatTableau['username_demandeur'],
            $objetFormatTableau['id_proposition'],
            $objetFormatTableau['message']
        );
    }

    public function selectAllByProposition(int $idProposition)
    {
        $sql = <<<SQL
        SELECT * 
            FROM demande_co_auteur 
            WHERE id_proposition = :id_proposition
        SQL;

        $pdo = DatabaseConnection::getPdo();
        $values = [
            'id_proposition' => $idProposition
        ];

        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->execute($values);

        $resultat = [];
        foreach ($pdoStatement as $ligne) {
            $resultat[] = $this->construire($ligne);
        }
        return $resultat;
    }
}
