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

        $this->deleteRedacteurs($question->getIdQuestion());
        foreach ($question->getRedacteurs() as $redacteur) {
            $this->insertRedacteur($question->getIdQuestion(), $redacteur->getIdUtilisateur());
        }

        $this->deleteVotants($question->getIdQuestion());
        foreach ($question->getVotants() as $votant) {
            $this->insertVotant($question->getIdQuestion(), $votant->getIdUtilisateur());
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

    public function getRedacteurs(int $idQuestion): array
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

    public function estRedacteur(int $idQuestion, int $idUtilisateur): bool
    {
        $sql = "SELECT COUNT(*) AS est_redacteur FROM redacteur WHERE id_question = :idQuestion AND id_redacteur = :idUtilisateur";
        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute([
            "idQuestion" => $idQuestion,
            "idUtilisateur" => $idUtilisateur
        ]);

        return $pdo->fetch()['est_redacteur'] > 0;
    }

    public function estVotant(int $idQuestion, int $idUtilisateur): bool
    {
        $sql = "SELECT COUNT(*) AS est_votant FROM votant WHERE id_question = :idQuestion AND id_votant = :idUtilisateur";
        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute([
            "idQuestion" => $idQuestion,
            "idUtilisateur" => $idUtilisateur
        ]);

        return $pdo->fetch()['est_votant'] > 0;
    }

    public function estCoAuteur(int $idQuestion, int $idUtilisateur) : bool
    {
        $sql = "SELECT COUNT(*) AS est_coauteur FROM co_auteur 
                                        WHERE id_paragraphe IN (SELECT id_paragraphe
                                        FROM paragraphe
                                        JOIN proposition ON proposition.id_proposition = paragraphe.id_proposition
                                        JOIN question ON question.id_question = proposition.id_question
                                        WHERE question.id_question = :idQuestion)
                AND id_utilisateur = :idUtilisateur";
        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute([
            "idQuestion" => $idQuestion,
            "idUtilisateur" => $idUtilisateur
        ]);

        return $pdo->fetch()['est_coauteur'] > 0;
    }

    public function aVote(int $idQuestion, int $idUtilisateur): bool
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
