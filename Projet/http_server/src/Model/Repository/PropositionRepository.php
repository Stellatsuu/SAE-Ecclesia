<?php

namespace App\SAE\Model\Repository;

use App\SAE\Config\Conf;
use App\SAE\Model\DataObject\AbstractDataObject;
use App\SAE\Model\DataObject\Proposition;
use App\SAE\Model\DataObject\Question;
use App\SAE\Model\DataObject\Utilisateur;

class PropositionRepository extends AbstractRepository {

    protected function getNomTable(): string
    {
        return "Proposition";
    }

    protected function getNomClePrimaire(): string
    {
        return "id_proposition";
    }

    protected function getNomsColonnes(): array
    {
        return [
            "titre_proposition",
            "id_redacteur",
            "id_question"

        ];
    }

    protected function construire(array $objetFormatTableau): Proposition
    {
        return new Proposition(
            $objetFormatTableau['id_proposition'],
            $objetFormatTableau['titre_proposition'],
            (new UtilisateurRepository())->select($objetFormatTableau['id_redacteur']),
            (new QuestionRepository())->select($objetFormatTableau['id_question']),
            (new ParagrapheRepository())->selectAllByProposition($objetFormatTableau['id_proposition'])
        );
    }

    public function selectByQuestionEtRedacteur(Question $question, int $idRedacteur): ?Proposition{
        $sql = "SELECT * FROM {$this->getNomTable()} WHERE id_redacteur = :id_redacteur AND id_question = :id_question";
        $values = [
            "id_redacteur" => $idRedacteur,
            "id_question" => $question->getIdQuestion()
        ];

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute($values);

        return static::construire($pdo->fetch());
    }
}