<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\Question;

class QuestionRepository {

    public static function construire(array $row): Question {
        $question = new Question(
            $row['idquestion'],
            $row['question'],
            $row['intitule'],
            $row['estvalide'],
            UtilisateurRepository::getUtilisateurParID($row['idutilisateur'])
        );
        return $question;
    }

    public static function getQuestions(): array {
        $pdo = DatabaseConnection::getPdo();
        $pdoStatement = $pdo->query("SELECT * FROM questions");

        $questions = [];
        foreach ($pdoStatement as $q) {
            $questions[] = static::construire($q);
        }
        return $questions;
    }

    public static function getQuestionById(int $idQuestion): ?Question {
        $pdo = DatabaseConnection::getPdo();
        $sql = "SELECT * FROM questions WHERE idquestion = :idQuestion";

        $pdoStatement = $pdo->prepare($sql);

        $values = array(
            "idQuestion" => $idQuestion
        );

        $pdoStatement->execute($values);

        $row = $pdoStatement->fetch();

        if ($row) {
            return static::construire($row);
        } else {
            return null;
        }
    }

    public static function sauvegarder(Question $question): void {
        $pdo = DatabaseConnection::getPdo();
        $sql = "INSERT INTO questions (question, intitule, estvalide, idutilisateur) VALUES (:question, :intitule, :estvalide, :idutilisateur)";

        $pdoStatement = $pdo->prepare($sql);

        $values = array(
            "question" => $question->getQuestion(),
            "intitule" => $question->getIntitule(),
            "estvalide" => $question->getEstValide(),
            "idutilisateur" => $question->getOrganisateur()->getIdUtilisateur()
        );

        $pdoStatement->execute($values);
    }




}
