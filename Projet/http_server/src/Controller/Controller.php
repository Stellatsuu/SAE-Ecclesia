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
            "contenu" => "form_attribuerQuestion.php",
            "question" => QuestionRepository::getPremiereQuestionNonValide(),
        ]);
    }

    public static function listerQuestions(): void
    {

        $questions = QuestionRepository::getQuestions();

        static::afficheVue("view.php", [
            "titre" => "Liste des questions",
            "contenu" => "listeQuestions.php",
            "questions" => $questions
        ]);
    }

    public static function refuserQuestion(): void
    {
        $idQuestion = intval($_POST['idQuestion']);
        QuestionRepository::supprimerParID($idQuestion);
        static::listerQuestions();
    }

    public static function accepterQuestion(): void
    {
        $idQuestion = intval($_POST['idQuestion']);

        QuestionRepository::valider($idQuestion);
        static::listerQuestions();
    }

    private static function afficheVue(string $cheminVue, array $parametres = []): void
    {
        extract($parametres); // Crée des variables à partir du tableau $parametres
        require __DIR__ . '/../view/' . $cheminVue;
    }
}
