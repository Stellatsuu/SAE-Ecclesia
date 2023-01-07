<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\AbstractDataObject;


abstract class AbstractRepository
{

    protected abstract function getNomTable(): string;
    protected abstract function getNomClePrimaire(): string;
    protected abstract function getNomsColonnes(): array;
    protected abstract function construire(array $objetFormatTableau): AbstractDataObject;


    /**
     * @param string $sql Requête sql à exécuter.
     * @param array $values Valeurs utilisées.
     * */
    private function getPdoStatement(string $sql, array $values): bool|\PDOStatement
    {
        $pdo = DatabaseConnection::getPdo();

        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->execute($values);

        return $pdoStatement;
    }

    /**
     * @return string Clé primaire composite de la table.
     * */
    private function genererConditionsClePrimaireComposite() : array
    {
        $conditionsArray = [];
        $values = [];
        for ($i = 0; $i < count($nomsClesPrimaires); $i++) {
            $cond = $nomsClesPrimaires[$i] . " = :valeurClePrimaire" . $i;
            $conditionsArray[] = $cond;
            $values["valeurClePrimaire" . $i] = $valeursClePrimaire[$i];
        }

         return [
             "conditions" => implode(" AND ", $conditionsArray),
             "values" => $values
         ];
    }

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
        if (count($params) == 1) {
            return $this->selectCleUnique($params[0]);
        } elseif (count($params) >= 2) {
            return $this->selectCleMultiple($params);
        } else {
            throw new \Exception("Au moins un paramètre est requis");
        }
    }

    private function selectCleUnique($valeurClePrimaire): ?AbstractDataObject
    {
        $nomTable = $this->getNomTable();
        $nomClePrimaire = $this->getNomClePrimaire();

        $sql = "SELECT * FROM $nomTable WHERE $nomClePrimaire = :valeurClePrimaire";
        $values = [
            'valeurClePrimaire' => $valeurClePrimaire
        ];

        return $this->executerSelect($sql, $values);
    }

    private function selectCleMultiple($valeursClePrimaire): ?AbstractDataObject
    {
        $nomTable = $this->getNomTable();
        $nomsClesPrimaires = explode(",", $this->getNomClePrimaire());

        if (count($valeursClePrimaire) != count($nomsClesPrimaires)) {
            throw new \Exception("Nombre de clés primaires incorrect");
        }

        $conditionsValues = $this->genererConditionsClePrimaireComposite();
        $conditions = $conditionsValues["conditions"];
        $values = $conditionsValues["values"];

        $sql = "SELECT * FROM $nomTable WHERE $conditions";

        return $this->executerSelect($sql, $values);
    }

    /**
     * @param string $sql Requête sql à exécuter.
     * @param array $values Valeurs utilisées.
     * */
    private function executerSelect(string $sql, array $values): ?AbstractDataObject
    {
        $pdoStatement = $this->getPdoStatement($sql, $values);

        $ligne = $pdoStatement->fetch();
        if ($ligne === false)
            return null;

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

        $this->getPdoStatement($sql, $values);
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


        $this->getPdoStatement($sql, $values);
    }

    public function delete(...$params): void
    {
        if (count($params) == 1) {
            $this->deleteCleUnique($params[0]);
        } elseif (count($params) >= 2) {
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
        $values = [
            'valeurClePrimaire' => $valeurClePrimaire
        ];

        $this->getPdoStatement($sql, $values);
    }

    private function deleteCleMultiple($valeursClePrimaire): void
    {
        $nomTable = $this->getNomTable();
        $nomsClesPrimaires = explode(",", $this->getNomClePrimaire());

        if (count($valeursClePrimaire) != count($nomsClesPrimaires)) {
            throw new \Exception("Nombre de clés primaires incorrect");
        }

        $conditionsValues = $this->genererConditionsClePrimaireComposite();
        $conditions = $conditionsValues["conditions"];
        $values = $conditionsValues["values"];

        $sql = "DELETE FROM $nomTable WHERE $conditions";

        $this->getPdoStatement($sql, $values);
    }
}
