<?php

namespace App\SAE\Model\Repository;

use App\SAE\Lib\ConnexionUtilisateur;
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
        $question->setDateDebutRedaction($row['date_debut_redaction'] == null ? null : new DateTime($row['date_debut_redaction']));
        $question->setDateFinRedaction($row['date_fin_redaction'] == null ? null : new DateTime($row['date_fin_redaction']));
        $question->setDateOuvertureVotes($row['date_ouverture_votes'] == null ? null : new DateTime($row['date_ouverture_votes']));
        $question->setDateFermetureVotes($row['date_fermeture_votes'] == null ? null : new DateTime($row['date_fermeture_votes']));
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

    public function selectAllListerQuestions(int $limit, int $offset, array $motsCles = [], array $tags = [], array $filtres = []): array
    {
        $conditionsEtValues = $this->genererConditionsEtValuesListerQuestions($motsCles, $tags, $filtres);
        $conditions = $conditionsEtValues['conditions'];
        $values = $conditionsEtValues['values'];

        $values['limit'] = $limit;
        $values['offset'] = $offset;

        $sql = <<<SQL
            SELECT *
                FROM question 
                WHERE $conditions
                ORDER BY date_debut_redaction DESC
                LIMIT :limit
                OFFSET :offset
        SQL;

        $stmt = DatabaseConnection::getPdo()->prepare($sql);
        $stmt->execute($values);

        $resultat = [];
        foreach ($stmt as $ligne) {
            $resultat[] = $this->construire($ligne);
        }

        return $resultat;
    }

    public function countAllListerQuestion(array $motsCles = [], array $tags = [], array $filtres = []): int
    {
        $conditionsEtValues = $this->genererConditionsEtValuesListerQuestions($motsCles, $tags, $filtres);
        $conditions = $conditionsEtValues['conditions'];
        $values = $conditionsEtValues['values'];

        $sql = <<<SQL
            SELECT COUNT(*)
                FROM question 
                WHERE $conditions
        SQL;

        $stmt = DatabaseConnection::getPdo()->prepare($sql);
        $stmt->execute($values);

        return $stmt->fetchColumn();
    }

    private function genererConditionsEtValuesListerQuestions(array $motsCles = [], array $tags = [], array $filtres = []) {
        $conditionsMCTags = [];
        $values = [];

        if (!empty($filtres)) {
            $conditionsPhases = [];

            //PHASES
            if (in_array("lecture", $filtres)) {
                $conditionsPhases[] = "getPhase(id_question) = 'lecture'";
            }
            if (in_array("redaction", $filtres)) {
                $conditionsPhases[] = "getPhase(id_question) = 'redaction'";
            }
            if (in_array("vote", $filtres)) {
                $conditionsPhases[] = "getPhase(id_question) = 'vote'";
            }
            if (in_array("resultat", $filtres)) {
                $conditionsPhases[] = "getPhase(id_question) = 'resultat'";
            }

            $conditionsRoles = [];

            //RÔLES
            if (ConnexionUtilisateur::estConnecte()) {
                if (in_array("redacteur", $filtres)) {
                    $conditionsRoles[] = "(EXISTS(SELECT * FROM redacteur WHERE username_redacteur = :username))";
                    $values['username'] = ConnexionUtilisateur::getUsername();
                }
                if (in_array("coauteur", $filtres)) {
                    $conditionsRoles[] = "(EXISTS(SELECT * FROM co_auteur WHERE username_co_auteur = :username))";
                    $values['username'] = ConnexionUtilisateur::getUsername();
                }
                if (in_array("votant", $filtres)) {
                    $conditionsRoles[] = "(EXISTS(SELECT * FROM votant WHERE username_votant = :username))";
                    $values['username'] = ConnexionUtilisateur::getUsername();
                }
            }

            $conditionsPhases = implode(" OR ", $conditionsPhases);
            $conditionsRoles = implode(" OR ", $conditionsRoles);

            $conditionsFiltres = array_filter([$conditionsPhases, $conditionsRoles], function ($condition) {
                return !empty($condition);
            });
            $conditionsFiltres = implode(" AND ", $conditionsFiltres);
        } else {
            $conditionsFiltres = "(date_debut_redaction IS NOT NULL AND date_debut_redaction <= CURRENT_TIMESTAMP)";
        }

        /////GESTION TAGS ET MOTS-CLES

        $tags = "{" . implode(",", $tags) . "}";
        $conditionsMCTags[] = "(tags @> :tags)";

        for ($i = 0; $i < count($motsCles); $i++) {
            $conditionsMCTags[] = "(LOWER(titre_question) LIKE :mot_cle_$i OR LOWER(description_question) LIKE :mot_cle_$i)";
        }

        $conditionsMCTags = implode(" AND ", $conditionsMCTags);
        $conditions = array_filter([$conditionsFiltres, $conditionsMCTags], function ($condition) {
            return !empty($condition);
        });
        $conditions = implode(" AND ", $conditions);

        $values['tags'] = $tags;

        for ($i = 0; $i < count($motsCles); $i++) {
            $values["mot_cle_$i"] = "%$motsCles[$i]%";
        }

        return ["conditions" => $conditions, "values" => $values];
    }
}
