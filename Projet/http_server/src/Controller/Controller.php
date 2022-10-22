<?php

namespace App\SAE\Controller;

use App\SAE\Model\Repository\QuestionRepository as QuestionRepository;
use App\SAE\Model\DataObject\Question as Question;
use App\SAE\Model\Repository\UtilisateurRepository as UtilisateurRepository;
use App\SAE\Model\DataObject\Utilisateur as Utilisateur;

class Controller
{

    public static function listerQuestions(): void
    {

        $questions = (new QuestionRepository)->selectAll();

        static::afficherVue("view.php", [
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

    public static function afficherDemandeQuestion(): void
    {
        static::afficherVue("view.php", [
            "titre" => "Demande de question",
            "contenu" => "demandeQuestion.php"
        ]);
    }

    public static function poserQuestion(): void
    {
        $questionText = $_POST['question'];
        $intitule = $_POST['intitule'];
        $idUtilisateur = intval($_POST['idUtilisateur']);

        $question = new Question(-1, $questionText, $intitule, false, (new UtilisateurRepository)->select($idUtilisateur));

        (new QuestionRepository)->insert($question);

        static::message("Question posée", "Votre question a bien été posée. Elle sera publiée après validation par un administrateur.");
    }

    private static function message(string $titre, string $message): void
    {
        static::afficherVue("view.php", [
            "titre" => $titre,
            "contenu" => "message.php",
            "message" => $message,
            "questions" => (new QuestionRepository)->selectAll()
        ]);
    }

    private static function afficherVue(string $cheminVue, array $parametres = []): void
    {
        extract($parametres); // Crée des variables à partir du tableau $parametres
        require __DIR__ . '/../view/' . $cheminVue;
    }
}
