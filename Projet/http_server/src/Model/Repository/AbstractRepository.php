<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\AbstractDataObject;


abstract class AbstractRepository
{

    protected abstract function getNomTable(): string;
    protected abstract function getNomClePrimaire(): string;
    protected abstract function getNomsColonnes(): array;
    protected abstract function construire(array $objetFormatTableau): AbstractDataObject;

    public function selectAll(): array
    {
        $nomTable = $this->getNomTable();

        $sql = "SELECT * FROM $nomTable";
        $pdo = DatabaseConnection::getPdo();

        $pdoStatement = $pdo->query($sql);

        $resultat = [];
        foreach ($pdoStatement as $ligne) {
            $resultat[] = $this->construire($ligne);
        }
        return $resultat;
    }

    public function select($valeurClePrimaire): ?AbstractDataObject
    {
        $nomTable = $this->getNomTable();
        $nomClePrimaire = $this->getNomClePrimaire();

        $sql = "SELECT * FROM $nomTable WHERE $nomClePrimaire = :valeurClePrimaire";
        $pdo = DatabaseConnection::getPdo();
        $values = [
            'valeurClePrimaire' => $valeurClePrimaire
        ];

        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->execute($values);

        $ligne = $pdoStatement->fetch();
        if ($ligne === false) {
            return null;
        }
        return $this->construire($ligne);
    }

    public function update(AbstractDataObject $object): void
    {
        $nomTable = $this->getNomTable();
        $nomClePrimaire = $this->getNomClePrimaire();
        $nomsColonnes = $this->getNomsColonnes();
        $values = $object->formatTableau();
        $values[$nomClePrimaire] = $object->getValeurClePrimaire();

        $sql = "UPDATE $nomTable SET ";
        foreach ($nomsColonnes as $colonne) {
            $sql .= "$colonne = :$colonne, ";
        }
        $sql = substr($sql, 0, -2);
        $sql .= " WHERE $nomClePrimaire = :$nomClePrimaire";

        $pdoStatement = DatabaseConnection::getPdo()->prepare($sql);
        $pdoStatement->execute($values);
    }

    public function insert(AbstractDataObject $object) : void {
        $nomTable = $this->getNomTable();
        $nomsColonnes = $this->getNomsColonnes();
        $values = $object->formatTableau();

        $sql = "INSERT INTO $nomTable (";
        foreach ($nomsColonnes as $colonne) {
            $sql .= "$colonne, ";
        }
        $sql = substr($sql, 0, -2);
        $sql .= ") VALUES (";
        foreach ($nomsColonnes as $colonne) {
            $sql .= ":$colonne, ";
        }
        $sql = substr($sql, 0, -2);
        $sql .= ")";

        
        $pdoStatement = DatabaseConnection::getPdo()->prepare($sql);
        $pdoStatement->execute($values);
    }

    public function delete($valeurClePrimaire): void
    {
        $nomTable = $this->getNomTable();
        $nomClePrimaire = $this->getNomClePrimaire();

        $sql = "DELETE FROM $nomTable WHERE $nomClePrimaire = :valeurClePrimaire";
        $pdo = DatabaseConnection::getPdo();
        $values = [
            'valeurClePrimaire' => $valeurClePrimaire
        ];

        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->execute($values);
    }
}
