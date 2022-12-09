<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\AbstractDataObject;
use App\SAE\Model\DataObject\Vote;
use App\SAE\Model\DataObject\Question;
use App\SAE\Model\DataObject\Proposition;

class VoteRepository extends AbstractRepository
{
    protected function getNomTable(): string
    {
        return "vote";
    }
    protected function getNomClePrimaire(): string
    {
        return "username_votant,id_proposition";
    }
    protected function getNomsColonnes(): array
    {
        return [
            "username_votant",
            "id_proposition",
            "valeur"
        ];
    }
    protected function construire(array $objetFormatTableau): Vote
    {
        return new Vote(
            $objetFormatTableau['id_proposition'],
            $objetFormatTableau['username_votant'],
            $objetFormatTableau['valeur']
        );
    }

    public function selectAllByQuestion(int $idQuestion): array
    {
        $sql = <<<SQL
        SELECT * 
            FROM vote v JOIN proposition p ON v.id_proposition = p.id_proposition
            WHERE p.id_question = :id_question
        SQL;

        $pdo = DatabaseConnection::getPdo();
        $values = [
            'id_question' => $idQuestion
        ];

        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->execute($values);

        $resultat = [];
        foreach ($pdoStatement as $ligne) {
            $resultat[] = $this->construire($ligne);
        }
        return $resultat;
    }

    public function selectAllByProposition(int $idProposition): array
    {
        $sql = <<<SQL
        SELECT * 
            FROM vote JOIN WHERE id_proposition = :id_proposition
        SQL;

        $pdo = DatabaseConnection::getPdo();
        $values = [
            'idProposition' => $idProposition
        ];

        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->execute($values);

        $resultat = [];
        foreach ($pdoStatement as $ligne) {
            $resultat[] = $this->construire($ligne);
        }
        return $resultat;
    }

    public function existsForQuestion(int $idQuestion, string $username): bool
    {
        $sql = <<<SQL
        SELECT COUNT(*) AS a_vote 
            FROM vote v JOIN proposition p 
                ON v.id_proposition = p.id_proposition 
            WHERE p.id_question = :idQuestion 
            AND v.username_votant = :username
        SQL;

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute([
            "idQuestion" => $idQuestion,
            "username" => $username
        ]);

        return $pdo->fetch()['a_vote'] > 0;
    }
}
