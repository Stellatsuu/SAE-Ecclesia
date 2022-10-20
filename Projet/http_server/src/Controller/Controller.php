<?php

namespace App\SAE\Controller;

use App\SAE\Model\DataObject\Question as Question;
use App\SAE\Model\DataObject\Utilisateur as Utilisateur;
use App\SAE\Model\Repository\QuestionRepository as QuestionRepository;
use App\SAE\Model\Repository\UtilisateurRepository as UtilisateurRepository;

class Controller
{

    public static function showForm_attribuerQuestion(): void
    {
        static::afficheVue("view.php", [
            "titre" => "Attribuer une question",
            "contenu" => "form_attribuerQuestion.php"
        ]);
    }

    public static function listerQuestions(): void {

        $questions = QuestionRepository::getQuestions();

        static::afficheVue("view.php", [
            "titre" => "Liste des questions",
            "contenu" => "listeQuestions.php",
            "questions" => $questions
        ]);

    }

    public static function attribuerQuestion(): void {
        $question = $_POST["question"];
        $intitule = $_POST["intitule"];
        $estValide = $_POST["estValide"];
        $organisateur = UtilisateurRepository::getUtilisateurParID($_POST["organisateur"]);

        $question = new Question(-1, $question, $intitule, $estValide, $organisateur);

        QuestionRepository::sauvegarder($question);

        static::afficheVue("view.php", [
            "titre" => "Question attribuée",
            "contenu" => "questionAttribuee.html"
        ]);
    }

    private static function afficheVue(string $cheminVue, array $parametres = []): void
    {
        extract($parametres); // Crée des variables à partir du tableau $parametres
        require __DIR__ . '/../view/' . $cheminVue;
    }
}
