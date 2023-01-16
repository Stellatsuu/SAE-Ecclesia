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

    public function selectAllListerQuestions(int $limit, int $offset, array $motsCles = [], array $tags = [], array $filtres = [], array $usernames = []): array
    {
        $conditionsEtValues = $this->genererConditionsEtValuesListerQuestions($motsCles, $tags, $filtres, $usernames);
        $conditions = $conditionsEtValues['conditions'];
        $values = $conditionsEtValues['values'];

        $values['limit'] = $limit;
        $values['offset'] = $offset;

        $sql = <<<SQL
            SELECT *
                FROM question q
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

    public function countAllListerQuestion(array $motsCles = [], array $tags = [], array $filtres = [], array $usernames = []): int
    {
        $conditionsEtValues = $this->genererConditionsEtValuesListerQuestions($motsCles, $tags, $filtres, $usernames);
        $conditions = $conditionsEtValues['conditions'];
        $values = $conditionsEtValues['values'];

        $sql = <<<SQL
            SELECT COUNT(*)
                FROM question q 
                WHERE $conditions
        SQL;

        $stmt = DatabaseConnection::getPdo()->prepare($sql);
        $stmt->execute($values);

        return $stmt->fetchColumn();
    }

    private function genererConditionsEtValuesListerQuestions(array $motsCles = [], array $tags = [], array $filtres = [], array $usernames = [])
    {

        $modeMesQuestions = in_array("mq", $filtres);

        $conditions = [];

        $connecte = ConnexionUtilisateur::estConnecte();


        //MODE MES QUESTIONS
        if ($connecte && $modeMesQuestions) {
            $conditions[] = "(username_organisateur = :username)";
            $values['username'] = ConnexionUtilisateur::getUsername();
        } else {
            $conditions[] = "(date_debut_redaction IS NOT NULL AND date_debut_redaction <= CURRENT_TIMESTAMP)";

            //USERNAMES
            $conditionsUsernames = [];
            for ($i = 0; $i < count($usernames); $i++) {
                $conditionsUsernames[] = "(username_organisateur = :username_$i)";
                $values["username_$i"] = "$usernames[$i]";
            }

            $conditionsUsernames = implode(" OR ", $conditionsUsernames);
            if($conditionsUsernames != "") $conditions[] = "($conditionsUsernames)";
        }


        //RÔLES
        $conditionsRoles = [];

        if ($connecte && in_array("redacteur", $filtres)) {
            $conditionsRoles[] = "(EXISTS(SELECT * FROM redacteur WHERE username_redacteur = :username AND id_question = q.id_question))";
            $values['username'] = ConnexionUtilisateur::getUsername();
        }

        if ($connecte && in_array("coauteur", $filtres)) {
            $conditionsRoles[] = "(EXISTS(SELECT * FROM co_auteur ca JOIN paragraphe pa ON ca.id_paragraphe = pa.id_paragraphe JOIN proposition p ON pa.id_proposition = p.id_proposition WHERE username_co_auteur = :username AND p.id_question = q.id_question))";
            $values['username'] = ConnexionUtilisateur::getUsername();
        }

        if ($connecte && in_array("votant", $filtres)) {
            $conditionsRoles[] = "(EXISTS(SELECT * FROM votant WHERE username_votant = :username AND id_question = q.id_question))";
            $values['username'] = ConnexionUtilisateur::getUsername();
        }

        $conditionsRoles = implode(" OR ", $conditionsRoles);
        if($conditionsRoles != "") $conditions[] = "($conditionsRoles)";


        //PHASES
        $conditionsPhases = [];

        if (in_array("non_remplie", $filtres)) $conditionsPhases[] = "getPhase(id_question) = 'nonRemplie'";

        if (in_array("attente", $filtres)) $conditionsPhases[] = "getPhase(id_question) = 'attente'";

        if (in_array("lecture", $filtres)) $conditionsPhases[] = "getPhase(id_question) = 'lecture'";

        if (in_array("redaction", $filtres)) $conditionsPhases[] = "getPhase(id_question) = 'redaction'";

        if (in_array("vote", $filtres)) $conditionsPhases[] = "getPhase(id_question) = 'vote'";

        if (in_array("resultat", $filtres)) $conditionsPhases[] = "getPhase(id_question) = 'resultat'";

        $conditionsPhases = implode(" OR ", $conditionsPhases);
        if($conditionsPhases != "") $conditions[] = "($conditionsPhases)";


        //TAGS
        $tags = "{" . implode(",", $tags) . "}";

        $conditions[] = "(tags @> :tags)";
        $values['tags'] = $tags;


        //MOTS-CLÉS
        for ($i = 0; $i < count($motsCles); $i++) {
            $conditions[] = "(LOWER(titre_question) LIKE :mot_cle_$i OR LOWER(description_question) LIKE :mot_cle_$i)";
            $values["mot_cle_$i"] = "%$motsCles[$i]%";
        }

        $conditions = implode(" AND ", $conditions);

        return ["conditions" => $conditions, "values" => $values];
    }
}
