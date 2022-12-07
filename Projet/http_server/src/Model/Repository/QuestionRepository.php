<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\Question;
use App\SAE\Model\SystemeVote\SystemeVoteFactory;
use DateTime;

class QuestionRepository extends AbstractRepository
{

    protected function getNomTable(): string
    {
        return 'question';
    }

    protected function getNomClePrimaire(): string
    {
        return 'id_question';
    }

    protected function getNomsColonnes(): array
    {
        return [
            'titre_question',
            'description_question',
            'id_organisateur',
            'date_debut_redaction',
            'date_fin_redaction',
            'date_ouverture_votes',
            'date_fermeture_votes',
            'systeme_vote'
        ];
    }

    public function construire(array $row): Question
    {
        $question = new Question(
            $row['id_question'],
            $row['titre_question'],
            $row['description_question'],
            $row['id_organisateur']
        );
        $question->setDateDebutRedaction($row['date_debut_redaction'] == NULL ? NULL : new DateTime($row['date_debut_redaction'])); 
        $question->setDateFinRedaction($row['date_fin_redaction'] == NULL ? NULL : new DateTime($row['date_fin_redaction']));
        $question->setDateOuvertureVotes($row['date_ouverture_votes'] == NULL ? NULL : new DateTime($row['date_ouverture_votes']));
        $question->setDateFermetureVotes($row['date_fermeture_votes'] == NULL ? NULL : new DateTime($row['date_fermeture_votes']));
        $question->setSystemeVote(SystemeVoteFactory::createSystemeVote($row['systeme_vote'], $question));
        return $question;
    }

    public function updateEbauche(Question $question): void
    {
        $pdo = DatabaseConnection::getPdo();

        $sql = "UPDATE question SET description_question = :description_question, date_debut_redaction = :date_debut_redaction, date_fin_redaction = :date_fin_redaction, date_ouverture_votes = :date_ouverture_votes, date_fermeture_votes = :date_fermeture_votes, systeme_vote = :systeme_vote WHERE id_question = :id_question";
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'description_question' => $question->getDescription(),
            'date_debut_redaction' => $question->getDateDebutRedaction()->format('Y-m-d H:i:s'),
            'date_fin_redaction' => $question->getDateFinRedaction()->format('Y-m-d H:i:s'),
            'date_ouverture_votes' => $question->getDateOuvertureVotes()->format('Y-m-d H:i:s'),
            'date_fermeture_votes' => $question->getDateFermetureVotes()->format('Y-m-d H:i:s'),
            'id_question' => $question->getIdQuestion(),
            'systeme_vote' => $question->getSystemeVote()->getNom()
        ];

        $pdoStatement->execute($values);

        (new SectionRepository)->deleteAllByQuestion($question->getIdQuestion());
        foreach ($question->getSections() as $section) {
            $section->setIdQuestion($question->getIdQuestion());
            (new SectionRepository)->insert($section);
        }

        (new RedacteurRepository)->deleteRedacteursParQuestion($question->getIdQuestion());
        foreach ($question->getRedacteurs() as $redacteur) {
            (new RedacteurRepository)->insert($question->getIdQuestion(), $redacteur->getIdUtilisateur());
        }

        (new VotantRepository)->deleteVotantsParQuestion($question->getIdQuestion());
        foreach ($question->getVotants() as $votant) {
            (new VotantRepository)->insert($question->getIdQuestion(), $votant->getIdUtilisateur());
        }
    }

    public function selectAllQuestionsParOrganisateur(int $idUtilisateur): array
    {
        $pdo = DatabaseConnection::getPdo();

        $sql = "SELECT * FROM question WHERE id_organisateur = :id_organisateur";
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'id_organisateur' => $idUtilisateur
        ];

        $pdoStatement->execute($values);

        $questions = [];
        foreach ($pdoStatement as $row) {
            $questions[] = $this->construire($row);
        }
        return $questions;
    }

    public function selectAllQuestionsFinies() : array{
        $sql = "SELECT *
                FROM question
                WHERE date_fermeture_votes <= CURRENT_TIMESTAMP";

        $pdo = DatabaseConnection::getPdo();

        $pdoStatement = $pdo->query($sql);

        $resultat = [];
        foreach ($pdoStatement as $ligne) {
            $resultat[] = $this->construire($ligne);
        }
        return $resultat;
    }
}
