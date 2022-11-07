<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\Question;

use DateTime;

class QuestionRepository extends AbstractRepository
{

    protected function getNomTable(): string
    {
        return 'question';
    }

    protected function getNomClePrimaire(): string
    {
        return 'idquestion';
    }

    protected function getNomsColonnes(): array
    {
        return [
            'titre',
            'intitule',
            'idutilisateur',
            'datedebutredaction',
            'datefinredaction',
            'dateouverturevotes',
            'datefermeturevotes'
        ];
    }

    public function construire(array $row): Question
    {
        $question = new Question(
            $row['idquestion'],
            $row['titre'],
            $row['intitule'],
            (new UtilisateurRepository)->select($row['idutilisateur']),
            (new SectionRepository)->selectAllByQuestion($row['idquestion']),
            $row['datedebutredaction'] === NULL ? NULL : new DateTime($row['datedebutredaction']),
            $row['datefinredaction'] === NULL ? NULL : new DateTime($row['datefinredaction']),
            $row['dateouverturevotes'] === NULL ? NULL : new DateTime($row['dateouverturevotes']),
            $row['datefermeturevotes'] === NULL ? NULL : new DateTime($row['datefermeturevotes'])
        );
        return $question;
    }

    public function insertEbauche(Question $question): void
    {
        $pdo = DatabaseConnection::getPdo();
        $sql = "INSERT INTO question (titre, intitule, idutilisateur) VALUES (:titre, :intitule, :idutilisateur)";

        $pdoStatement = $pdo->prepare($sql);

        $values = [
            'titre' => $question->getTitre(),
            'intitule' => $question->getIntitule(),
            'idutilisateur' => $question->getOrganisateur()->getIdUtilisateur()
        ];

        $pdoStatement->execute($values);
    }

    public function updateEbauche(Question $question): void
    {
        $pdo = DatabaseConnection::getPdo();
        
        $sql = "UPDATE question SET intitule = :intitule, datedebutredaction = :datedebutredaction, datefinredaction = :datefinredaction, dateouverturevotes = :dateouverturevotes, datefermeturevotes = :datefermeturevotes WHERE idquestion = :idquestion";
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'intitule' => $question->getIntitule(),
            'datedebutredaction' => $question->getDateDebutRedaction()->format('Y-m-d H:i:s'),
            'datefinredaction' => $question->getDateFinRedaction()->format('Y-m-d H:i:s'),
            'dateouverturevotes' => $question->getDateOuvertureVotes()->format('Y-m-d H:i:s'),
            'datefermeturevotes' => $question->getDateFermetureVotes()->format('Y-m-d H:i:s'),
            'idquestion' => $question->getIdQuestion()
        ];

        $sql = "DELETE FROM section WHERE idquestion = :idquestion";
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'idquestion' => $question->getIdQuestion()
        ];
        $pdoStatement->execute($values);

        $sql = "INSERT INTO section (idquestion, nomsection) VALUES (:idquestion, :nomsection)";
        $pdoStatement = $pdo->prepare($sql);
        foreach ($question->getSections() as $section) {
            $values = [
                'idquestion' => $question->getIdQuestion(),
                'nomsection' => $section->getNomSection()
            ];
            echo "section : " . $section->getNomSection();
            $pdoStatement->execute($values);
        }
    }

    public function getQuestionsParOrganisateur(int $idUtilisateur) : array {
        $pdo = DatabaseConnection::getPdo();

        $sql = "SELECT * FROM question WHERE idutilisateur = :idutilisateur";
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'idutilisateur' => $idUtilisateur
        ];

        $pdoStatement->execute($values);

        $questions = [];
        foreach ($pdoStatement as $row) {
            $questions[] = $this->construire($row);
        }
        return $questions;
    }
}
