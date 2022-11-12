<?php

namespace App\SAE\Controller;

use App\SAE\Model\Repository\QuestionRepository as QuestionRepository;
use App\SAE\Model\DataObject\Question as Question;
use App\SAE\Model\DataObject\Section;
use App\SAE\Model\Repository\UtilisateurRepository as UtilisateurRepository;
use DateTime;
use DateInterval;


class QuestionController extends Controller
{
    
    public static function afficherFormulairePoserQuestion(): void
    {
        $idQuestion = intval($_GET['idQuestion']);
        $question = Question::toQuestion((new QuestionRepository)->select($idQuestion));

        if (
            $question->getDateDebutRedaction() === null
            || $question->getDateFinRedaction() === null
            || $question->getDateOuvertureVotes() === null
            || $question->getDateFermetureVotes() === null
        ) {

            $question->setDateDebutRedaction((new DateTime())->add(new DateInterval('P1D')));
            $question->setDateFinRedaction((new DateTime())->add(new DateInterval('P8D')));
            $question->setDateOuvertureVotes((new DateTime())->add(new DateInterval('P8D')));
            $question->setDateFermetureVotes((new DateTime())->add(new DateInterval('P15D')));
        }

        $datesFormatees = array(
            "dateDebutRedaction" => $question->getDateDebutRedaction()->format("Y-m-d"),
            "dateFinRedaction" => $question->getDateFinRedaction()->format("Y-m-d"),
            "dateOuvertureVotes" => $question->getDateOuvertureVotes()->format("Y-m-d"),
            "dateFermetureVotes" => $question->getDateFermetureVotes()->format("Y-m-d")
        );

        static::afficherVue("view.php", [
            "titrePage" => "Poser une question",
            "contenuPage" => "formulairePoserQuestion.php",
            "question" => $question,
            "datesFormatees" => $datesFormatees
        ]);
    }

    public static function poserQuestion(): void
    {

        $idQuestion = intval($_POST['idQuestion']);
        $titre = $_POST['titre'];
        $intitule = $_POST['intitule'];
        $idUtilisateur = intval($_POST['idUtilisateur']);

        $nbSections = 1;
        $sections = [];

        // Remplissage du _GET pour les messages d'erreur
        $_GET["idQuestion"] = $idQuestion;

        while (isset($_POST['nomSection' . $nbSections]) && isset($_POST['descriptionSection' . $nbSections])) {
            $nomSection = $_POST['nomSection' . $nbSections];
            $descriptionSection = $_POST['descriptionSection' . $nbSections];

            if ($nomSection == "" || $descriptionSection == "") {
                static::error("afficherFormulairePoserQuestion", "Veuillez remplir tous les champs");
                return;
            }

            if (strlen($nomSection) > 50) {
                static::error("afficherFormulairePoserQuestion", "Le nom de la section ne doit pas dépasser 50 caractères");
                return;
            }

            $section = new Section(-1, -1, $nomSection, $descriptionSection);
            $sections[] = $section;
            $nbSections++;
        }

        if (
            !preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $_POST['dateDebutRedaction'])
            || !preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $_POST['dateFinRedaction'])
            || !preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $_POST['dateOuvertureVotes'])
            || !preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $_POST['dateFermetureVotes'])
        ) {
            static::error("afficherFormulairePoserQuestion", "Veuillez entrer des dates valides");
            return;
        }

        if (
            !preg_match("/^[0-9]{2}:[0-9]{2}$/", $_POST['heureDebutRedaction'])
            || !preg_match("/^[0-9]{2}:[0-9]{2}$/", $_POST['heureFinRedaction'])
            || !preg_match("/^[0-9]{2}:[0-9]{2}$/", $_POST['heureOuvertureVotes'])
            || !preg_match("/^[0-9]{2}:[0-9]{2}$/", $_POST['heureFermetureVotes'])
        ) {
            static::error("afficherFormulairePoserQuestion", "Veuillez entrer des heures valides");
            return;
        }

        $dateDebutRedaction = new DateTime($_POST['dateDebutRedaction']);
        $heureDebutRedaction = preg_split('/\D/', $_POST['heureDebutRedaction']);
        $dateDebutRedaction->setTime($heureDebutRedaction[0], $heureDebutRedaction[1]);

        $dateFinRedaction = new DateTime($_POST['dateFinRedaction']);
        $heureFinRedaction = preg_split('/\D/', $_POST['heureFinRedaction']);
        $dateFinRedaction->setTime($heureFinRedaction[0], $heureFinRedaction[1]);

        $dateOuvertureVotes = new DateTime($_POST['dateOuvertureVotes']);
        $heureOuvertureVotes = preg_split('/\D/', $_POST['heureOuvertureVotes']);
        $dateOuvertureVotes->setTime($heureOuvertureVotes[0], $heureOuvertureVotes[1]);

        $dateFermetureVotes = new DateTime($_POST['dateFermetureVotes']);
        $heureFermetureVotes = preg_split('/\D/', $_POST['heureFermetureVotes']);
        $dateFermetureVotes->setTime($heureFermetureVotes[0], $heureFermetureVotes[1]);

        $dateCoherentes =
            $dateDebutRedaction < $dateFinRedaction
            && $dateFinRedaction <= $dateOuvertureVotes
            && $dateOuvertureVotes < $dateFermetureVotes
            && $dateDebutRedaction > (new DateTime("now"));

        // Vérification des données
        if (!$dateCoherentes) {
            static::error("afficherFormulairePoserQuestion", "Les dates ne sont pas cohérentes");
            return;
        }

        if ($sections == []) {
            static::error("afficherFormulairePoserQuestion", "Au moins une section est requise");
            return;
        }

        if ($titre == "" || $intitule == "") {
            static::error("afficherFormulairePoserQuestion", "Veuillez remplir tous les champs");
            return;
        }

        if (strlen($titre) > 100) {
            static::error("afficherFormulairePoserQuestion", "Le titre ne doit pas dépasser 100 caractères");
            return;
        }

        $question = new Question(
            $idQuestion,
            $titre,
            $intitule,
            (new UtilisateurRepository)->select($idUtilisateur),
            $sections,
            $dateDebutRedaction,
            $dateFinRedaction,
            $dateOuvertureVotes,
            $dateFermetureVotes
        );

        (new QuestionRepository)->updateEbauche($question);

        $_GET['idUtilisateur'] = $idUtilisateur;
        static::message("listerMesQuestions", "La question a été posée");
    }

    public static function listerMesQuestions()
    {
        $idUtilisateur = intval($_GET['idUtilisateur']);
        $questions = (new QuestionRepository)->getQuestionsParOrganisateur($idUtilisateur);

        static::afficherVue("view.php", [
            "titrePage" => "Mes questions",
            "contenuPage" => "listeMesQuestions.php",
            "questions" => $questions
        ]);
    }
}
