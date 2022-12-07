<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\AbstractDataObject;
use App\SAE\Model\DataObject\Vote;
use App\SAE\Model\DataObject\Question;
use App\SAE\Model\DataObject\Proposition;

class VoteRepository extends AbstractRepository
{
    protected function getNomTable(): string {
        return "vote";
    }
    protected function getNomClePrimaire(): string {
        return "id_votant,id_proposition";
    }
    protected function getNomsColonnes(): array {
        return [
            "id_votant",
            "id_proposition",
            "valeur"
        ];
    }
    protected function construire(array $objetFormatTableau) : Vote {
        $proposition = (new PropositionRepository())->select($objetFormatTableau["id_proposition"]);
        $votant = (new UtilisateurRepository())->select($objetFormatTableau["id_votant"]);
        return new Vote($proposition, $votant, $objetFormatTableau["valeur"]);
    }

    public function selectAllByQuestion(Question $question) : array {
        $nomTable = $this->getNomTable();
        $idQuestion = $question->getIdQuestion();

        $sql = "SELECT * FROM $nomTable v JOIN proposition p ON v.id_proposition = p.id_proposition WHERE p.id_question = :idQuestion";
        $pdo = DatabaseConnection::getPdo();
        $values = [
            'idQuestion' => $idQuestion
        ];

        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->execute($values);

        $resultat = [];
        foreach ($pdoStatement as $ligne) {
            $resultat[] = $this->construire($ligne);
        }
        return $resultat;
    }

    public function selectAllByProposition(Proposition $proposition) : array {
        $nomTable = $this->getNomTable();
        $idProposition = $proposition->getIdProposition();

        $sql = "SELECT * FROM $nomTable WHERE id_proposition = :idProposition";
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

    public function existeVoteSurQuestion(int $idQuestion, int $idUtilisateur): bool
    {
        $sql = "SELECT COUNT(*) AS a_vote 
                    FROM vote v JOIN proposition p 
                    ON v.id_proposition = p.id_proposition 
                WHERE p.id_question = :idQuestion 
                    AND v.id_votant = :idUtilisateur";
        
        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute([
            "idQuestion" => $idQuestion,
            "idUtilisateur" => $idUtilisateur
        ]);

        return $pdo->fetch()['a_vote'] > 0;
    }
}
