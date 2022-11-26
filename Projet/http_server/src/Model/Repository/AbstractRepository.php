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

    public function select(...$params): ?AbstractDataObject
    {
        if(count($params) == 1) {
            return $this->selectCleUnique($params[0]);
        } else if(count($params) >= 2) {
            return $this->selectCleMultiple($params);
        } else {
            throw new \Exception("Au moins un paramètre est requis");
        }
    }

    private function selectCleUnique($valeurClePrimaire)
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

    private function selectCleMultiple($valeursClePrimaire)
    {
        $nomTable = $this->getNomTable();
        $nomsClesPrimaires = explode(",", $this->getNomClePrimaire());

        if (count($valeursClePrimaire) != count($nomsClesPrimaires)) {
            throw new \Exception("Nombre de clés primaires incorrect");
        }

        $conditionsArray = [];
        for ($i = 0; $i < count($nomsClesPrimaires); $i++) {
            $cond = $nomsClesPrimaires[$i] . " = :valeurClePrimaire" . $i;
            $conditionsArray[] = $cond;
            $values["valeurClePrimaire" . $i] = $valeursClePrimaire[$i];
        }

        $conditions = implode(" AND ", $conditionsArray);

        $sql = "SELECT * FROM $nomTable WHERE $conditions";

        $pdo = DatabaseConnection::getPdo();

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

    public function insert(AbstractDataObject $object): void
    {
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

    public function delete(...$params): void
    {
        if(count($params) == 1) {
            $this->deleteCleUnique($params[0]);
        } else if(count($params) >= 2) {
            $this->deleteCleMultiple($params);
        } else {
            throw new \Exception("Au moins un paramètre est requis");
        }
    }

    private function deleteCleUnique($valeurClePrimaire): void
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

    private function deleteCleMultiple($valeursClePrimaire): void
    {
        $nomTable = $this->getNomTable();
        $nomsClesPrimaires = explode(",", $this->getNomClePrimaire());

        if (count($valeursClePrimaire) != count($nomsClesPrimaires)) {
            throw new \Exception("Nombre de clés primaires incorrect");
        }

        $conditionsArray = [];
        for ($i = 0; $i < count($nomsClesPrimaires); $i++) {
            $cond = $nomsClesPrimaires[$i] . " = :valeurClePrimaire" . $i;
            $conditionsArray[] = $cond;
            $values["valeurClePrimaire" . $i] = $valeursClePrimaire[$i];
        }

        $conditions = implode(" AND ", $conditionsArray);

        $sql = "DELETE FROM $nomTable WHERE $conditions";

        $pdo = DatabaseConnection::getPdo();

        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->execute($values);
    }
}
