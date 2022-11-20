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
            'date_fermeture_votes'
        ];
    }

    public function construire(array $row): Question
    {
        $question = new Question(
            $row['id_question'],
            $row['titre_question'],
            $row['description_question'],
            (new UtilisateurRepository)->select($row['id_organisateur']),
            (new SectionRepository)->selectAllByQuestion($row['id_question']),
            static::getRedacteur($row['id_question']),
            static::getVotants($row['id_question']),
            $row['date_debut_redaction'] === NULL ? NULL : new DateTime($row['date_debut_redaction']),
            $row['date_fin_redaction'] === NULL ? NULL : new DateTime($row['date_fin_redaction']),
            $row['date_ouverture_votes'] === NULL ? NULL : new DateTime($row['date_ouverture_votes']),
            $row['date_fermeture_votes'] === NULL ? NULL : new DateTime($row['date_fermeture_votes'])
        );
        return $question;
    }

    public function insertEbauche(Question $question): void
    {
        $pdo = DatabaseConnection::getPdo();
        $sql = "INSERT INTO question (titre_question, description_question, id_organisateur) VALUES (:titre_question, :description_question, :id_organisateur)";

        $pdoStatement = $pdo->prepare($sql);

        $values = [
            'titre_question' => $question->getTitre(),
            'description_question' => $question->getDescription(),
            'id_organisateur' => $question->getOrganisateur()->getIdUtilisateur()
        ];

        $pdoStatement->execute($values);
    }

    public function updateEbauche(Question $question): void
    {
        $pdo = DatabaseConnection::getPdo();

        $sql = "UPDATE question SET description_question = :description_question, date_debut_redaction = :date_debut_redaction, date_fin_redaction = :date_fin_redaction, date_ouverture_votes = :date_ouverture_votes, date_fermeture_votes = :date_fermeture_votes WHERE id_question = :id_question";
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'description_question' => $question->getDescription(),
            'date_debut_redaction' => $question->getDateDebutRedaction()->format('Y-m-d H:i:s'),
            'date_fin_redaction' => $question->getDateFinRedaction()->format('Y-m-d H:i:s'),
            'date_ouverture_votes' => $question->getDateOuvertureVotes()->format('Y-m-d H:i:s'),
            'date_fermeture_votes' => $question->getDateFermetureVotes()->format('Y-m-d H:i:s'),
            'id_question' => $question->getIdQuestion()
        ];

        $pdoStatement->execute($values);

        (new SectionRepository)->deleteAllByQuestion($question->getIdQuestion());

        foreach ($question->getSections() as $section) {
            $section->setIdQuestion($question->getIdQuestion());
            (new SectionRepository)->insert($section);
        }

        static::deleteRedacteurs($question->getIdQuestion());
        foreach ($question->getResponsables() as $redacteur) {
            static::insertRedacteur($question->getIdQuestion(), $redacteur->getIdUtilisateur());
        }

        static::deleteVotants($question->getIdQuestion());
        foreach ($question->getVotants() as $votant) {
            static::insertVotant($question->getIdQuestion(), $votant->getIdUtilisateur());
        }
    }

    public function getQuestionsParOrganisateur(int $idUtilisateur): array
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

    public function deleteRedacteurs(int $idQuestion): void
    {
        $pdo = DatabaseConnection::getPdo();

        $sql = "DELETE FROM redacteur WHERE id_question = :id_question";
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'id_question' => $idQuestion
        ];

        $pdoStatement->execute($values);
    }

    public function insertRedacteur(int $idQuestion, int $idUtilisateur): void
    {
        $pdo = DatabaseConnection::getPdo();

        $sql = "INSERT INTO redacteur (id_question, id_redacteur) VALUES (:id_question, :id_redacteur)";
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'id_question' => $idQuestion,
            'id_redacteur' => $idUtilisateur
        ];

        $pdoStatement->execute($values);
    }

    public function getRedacteur(int $idQuestion): array
    {
        $pdo = DatabaseConnection::getPdo();

        $sql = "SELECT id_redacteur FROM redacteur WHERE id_question = :id_question";
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'id_question' => $idQuestion
        ];

        $pdoStatement->execute($values);

        $redacteurs = [];
        foreach ($pdoStatement as $row) {
            $redacteurs[] = (new UtilisateurRepository)->select($row['id_redacteur']);
        }
        return $redacteurs;
    }

    public function deleteVotants(int $idQuestion): void
    {
        $pdo = DatabaseConnection::getPdo();

        $sql = "DELETE FROM votant WHERE id_question = :id_question";
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'id_question' => $idQuestion
        ];

        $pdoStatement->execute($values);
    }

    public function insertVotant(int $idQuestion, int $idUtilisateur): void
    {
        $pdo = DatabaseConnection::getPdo();

        $sql = "INSERT INTO votant (id_question, id_votant) VALUES (:id_question, :id_votant)";
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'id_question' => $idQuestion,
            'id_votant' => $idUtilisateur
        ];

        $pdoStatement->execute($values);
    }

    public function getVotants(int $idQuestion): array
    {
        $pdo = DatabaseConnection::getPdo();

        $sql = "SELECT id_votant FROM votant WHERE id_question = :id_question";
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'id_question' => $idQuestion
        ];

        $pdoStatement->execute($values);

        $votants = [];
        foreach ($pdoStatement as $row) {
            $votants[] = (new UtilisateurRepository)->select($row['id_votant']);
        }
        return $votants;
    }
}
