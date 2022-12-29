<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\AbstractDataObject;
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
            'username_organisateur',
            'date_debut_redaction',
            'date_fin_redaction',
            'date_ouverture_votes',
            'date_fermeture_votes',
            'systeme_vote',
            'tags'
        ];
    }

    public function construire(array $row): Question
    {
        $question = new Question(
            $row['id_question'],
            $row['titre_question'],
            $row['description_question'],
            $row['username_organisateur']
        );
        $question->setDateDebutRedaction($row['date_debut_redaction'] == NULL ? NULL : new DateTime($row['date_debut_redaction']));
        $question->setDateFinRedaction($row['date_fin_redaction'] == NULL ? NULL : new DateTime($row['date_fin_redaction']));
        $question->setDateOuvertureVotes($row['date_ouverture_votes'] == NULL ? NULL : new DateTime($row['date_ouverture_votes']));
        $question->setDateFermetureVotes($row['date_fermeture_votes'] == NULL ? NULL : new DateTime($row['date_fermeture_votes']));
        $question->setSystemeVote(SystemeVoteFactory::createSystemeVote($row['systeme_vote'], $question));
        $question->setTags($row['tags']);
        return $question;
    }

    public function update(AbstractDataObject $question): void
    {
        parent::update($question);

        $question = Question::castIfNotNull($question);
        $sections = $question->getSections();
        $redacteurs = $question->getRedacteurs();
        $votants = $question->getVotants();

        (new SectionRepository)->deleteAllByQuestion($question->getIdQuestion());
        foreach ($sections as $section) {
            $section->setIdQuestion($question->getIdQuestion());
            (new SectionRepository)->insert($section);
        }

        (new RedacteurRepository)->deleteAllByQuestion($question->getIdQuestion());
        foreach ($redacteurs as $redacteur) {
            (new RedacteurRepository)->insert($question->getIdQuestion(), $redacteur->getUsername());
        }

        (new VotantRepository)->deleteAllByQuestion($question->getIdQuestion());
        foreach ($votants as $votant) {
            (new VotantRepository)->insert($question->getIdQuestion(), $votant->getUsername());
        }
    }

    public function updateSansTablesAssociees(AbstractDataObject $question): void
    {
        parent::update($question);
    }

    public function selectAllByOrganisateur(string $username): array
    {
        $sql = <<<SQL
            SELECT *
                FROM question
                WHERE username_organisateur = :username_organisateur
        SQL;

        $pdo = DatabaseConnection::getPdo();
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'username_organisateur' => $username
        ];

        $pdoStatement->execute($values);

        $questions = [];
        foreach ($pdoStatement as $row) {
            $questions[] = $this->construire($row);
        }
        return $questions;
    }

    public function selectAllFinies(): array
    {
        $sql = <<<SQL
            SELECT *
                FROM question
                WHERE date_fermeture_votes <= CURRENT_TIMESTAMP
                ORDER BY date_fermeture_votes DESC
        SQL;

        $pdo = DatabaseConnection::getPdo();
        $pdoStatement = $pdo->query($sql);

        $resultat = [];
        foreach ($pdoStatement as $ligne) {
            $resultat[] = $this->construire($ligne);
        }
        return $resultat;
    }

    public function selectAllLimitOffset(int $limit, int $offset, array $motsCles = [], array $tags = [], array $filtres = []): array
    {
        $conditions = [];
        for ($i = 0; $i < count($motsCles); $i++) {
            $conditions[] = "AND (LOWER(titre_question) LIKE :mot_cle_$i OR LOWER(description_question) LIKE :mot_cle_$i)";
        }

        $tags="{" . implode(",",$tags) . "}";
        $conditions[] = "AND tags @> :tags";

        $conditions = implode(' ', $conditions);


        


        $sql = <<<SQL
            SELECT *
                FROM question 
                WHERE date_debut_redaction IS NOT NULL
                AND date_debut_redaction <= CURRENT_TIMESTAMP
                $conditions
                ORDER BY date_debut_redaction DESC
                LIMIT :limit
                OFFSET :offset
        SQL;

        $pdo = DatabaseConnection::getPdo();
        $pdoStatement = $pdo->prepare($sql);
        $values = [
            'limit' => $limit,
            'offset' => $offset,
            'tags' => $tags,
        ];

        for ($i = 0; $i < count($motsCles); $i++) {
            $values["mot_cle_$i"] = "%$motsCles[$i]%";
        }

        $pdoStatement->execute($values);
        $resultat = [];
        foreach ($pdoStatement as $ligne) {
            $resultat[] = $this->construire($ligne);
        }
        return $resultat;
    }

    public function countAllPhaseRedactionOuPlus(array $motsCles = [], array $tags=[]): int
    {
        $conditions = [];
        for ($i = 0; $i < count($motsCles); $i++) {
            $conditions[] = "AND (LOWER(titre_question) LIKE :mot_cle_$i OR LOWER(description_question) LIKE :mot_cle_$i)";
        }

        $tags="{" . implode(",",$tags) . "}";
        $conditions[] = "AND tags @> :tags";

        $conditions = implode(' ', $conditions);

        $sql = <<<SQL
            SELECT COUNT(*)
                FROM question
                WHERE date_debut_redaction IS NOT NULL
                AND date_debut_redaction <= CURRENT_TIMESTAMP
                $conditions
        SQL;

        $values = [
            'tags' => $tags,
        ];

        for ($i = 0; $i < count($motsCles); $i++) {
            $values["mot_cle_$i"] = "%$motsCles[$i]%";
        }

        $pdo = DatabaseConnection::getPdo();
        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->execute($values);

        return $pdoStatement->fetchColumn();
    }
}
