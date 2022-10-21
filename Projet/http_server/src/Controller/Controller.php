<?php

namespace App\SAE\Controller;

use App\SAE\Model\DataObject\Question as Question;
use App\SAE\Model\DataObject\Utilisateur as Utilisateur;
use App\SAE\Model\Repository\QuestionRepository as QuestionRepository;
use App\SAE\Model\Repository\UtilisateurRepository as UtilisateurRepository;

class Controller
{

    public static function listerQuestions(): void
    {

        $questions = (new QuestionRepository)->selectAll();

        static::afficheVue("view.php", [
            "titre" => "Liste des questions",
            "contenu" => "listeQuestions.php",
            "questions" => $questions
        ]);
    }

    public static function refuserQuestion(): void
    {
        $idQuestion = intval($_GET['idQuestion']);
        (new QuestionRepository)->delete($idQuestion);
        static::listerQuestions();
    }

    public static function accepterQuestion(): void
    {
        $idQuestion = intval($_GET['idQuestion']);

        $question = (new QuestionRepository)->select($idQuestion);
        $question->setEstValide(true);

        (new QuestionRepository)->update($question);
        static::listerQuestions();
    }

    private static function afficheVue(string $cheminVue, array $parametres = []): void
    {
        extract($parametres); // Crée des variables à partir du tableau $parametres
        require __DIR__ . '/../view/' . $cheminVue;
    }
}
