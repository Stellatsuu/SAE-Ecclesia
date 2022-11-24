<?php

namespace App\SAE\Controller;

use App\SAE\Model\Repository\QuestionRepository as QuestionRepository;
use App\SAE\Model\DataObject\Question as Question;
use App\SAE\Model\DataObject\Section;
use App\SAE\Model\DataObject\Utilisateur;
use App\SAE\Model\Repository\UtilisateurRepository as UtilisateurRepository;
use App\SAE\Lib\PhaseQuestion as Phase;
use DateTime;
use DateInterval;

class QuestionController extends MainController
{

    public static function afficherFormulairePoserQuestion(): void
    {
        if (!is_numeric($_GET['idQuestion'])) {
            static::error("afficherAccueil", "Aucune question n'a été sélectionnée");
            return;
        }

        $idQuestion = intval($_GET['idQuestion']);
        $question = Question::toQuestion((new QuestionRepository)->select($idQuestion));
        $utilisateurs = (new UtilisateurRepository)->selectAll();

        if ($question === null) {
            static::error("afficherAccueil", "La question n'existe pas");
            return;
        }

        //Vivement les sessions
        //$idUtilisateur = $_SESSION['idUtilisateur']; 
        $idUtilisateur = $question->getOrganisateur()->getIdUtilisateur();
        $_GET['idUtilisateur'] = $idUtilisateur;

        $phase = $question->getPhase();
        switch ($phase) {
            case Phase::Redaction:
                static::error("listerMesQuestions", "La question est en phase de rédaction. Vous ne pouvez plus la modifier.");
                return;
                break;
            case Phase::Lecture:
                static::error("listerMesQuestions", "La question est en phase de lecture. Vous ne pouvez plus la modifier.");
                return;
                break;
            case Phase::Vote:
                static::error("listerMesQuestions", "La question est en phase de vote. Vous ne pouvez plus la modifier.");
                return;
                break;
            case Phase::Resultat:
                static::error("listerMesQuestions", "La question est terminée. Vous ne pouvez plus la modifier.");
                return;
                break;
            case Phase::NonRemplie:
                $question->setDateDebutRedaction((new DateTime())->add(new DateInterval('P1D'))->setTime(16, 0, 0));
                $question->setDateFinRedaction((new DateTime())->add(new DateInterval('P8D'))->setTime(16, 0, 0));
                $question->setDateOuvertureVotes((new DateTime())->add(new DateInterval('P8D'))->setTime(16, 0, 0));
                $question->setDateFermetureVotes((new DateTime())->add(new DateInterval('P15D'))->setTime(16, 0, 0));
                break;
        }

        $datesFormatees = array(
            "dateDebutRedaction" => $question->getDateDebutRedaction()->format("Y-m-d"),
            "dateFinRedaction" => $question->getDateFinRedaction()->format("Y-m-d"),
            "dateOuvertureVotes" => $question->getDateOuvertureVotes()->format("Y-m-d"),
            "dateFermetureVotes" => $question->getDateFermetureVotes()->format("Y-m-d")
        );

        $heuresFormatees = array(
            "heureDebutRedaction" => $question->getDateDebutRedaction()->format("H:i"),
            "heureFinRedaction" => $question->getDateFinRedaction()->format("H:i"),
            "heureOuvertureVotes" => $question->getDateOuvertureVotes()->format("H:i"),
            "heureFermetureVotes" => $question->getDateFermetureVotes()->format("H:i")
        );

        static::afficherVue("view.php", [
            "titrePage" => "Poser une question",
            "contenuPage" => "formulairePoserQuestion.php",
            "question" => $question,
            "utilisateurs" => $utilisateurs,
            "datesFormatees" => $datesFormatees,
            "heuresFormatees" => $heuresFormatees
        ]);
    }

    public static function poserQuestion(): void
    {
        if (!is_numeric($_POST['idQuestion'])) {
            static::error("afficherAccueil", "Aucune question n'a été sélectionnée");
            return;
        }
        if (!isset($_POST['description'])) {
            static::error("afficherAccueil", "Veuillez remplir tous les champs");
            return;
        }

        $idQuestion = intval($_POST['idQuestion']);
        $description = $_POST['description'];

        $question = Question::toQuestion((new QuestionRepository)->select($idQuestion));

        $phase = $question->getPhase();
        switch ($phase) {
            case Phase::Redaction:
                static::error("listerMesQuestions", "La question est en phase de rédaction. Vous ne pouvez plus la modifier.");
                return;
                break;
            case Phase::Lecture:
                static::error("listerMesQuestions", "La question est en phase de lecture. Vous ne pouvez plus la modifier.");
                return;
                break;
            case Phase::Vote:
                static::error("listerMesQuestions", "La question est en phase de vote. Vous ne pouvez plus la modifier.");
                return;
                break;
            case Phase::Resultat:
                static::error("listerMesQuestions", "La question est terminée. Vous ne pouvez plus la modifier.");
                return;
                break;
        }

        // Remplissage du _GET pour les messages d'erreur
        $_GET["idQuestion"] = $idQuestion;

        $nbSections = 1;
        $sections = [];
        while (isset($_POST['nomSection' . $nbSections]) && isset($_POST['descriptionSection' . $nbSections])) {
            $nomSection = $_POST['nomSection' . $nbSections];
            $descriptionSection = $_POST['descriptionSection' . $nbSections];

            if ($nomSection == "" || $descriptionSection == "") {
                static::error("afficherFormulairePoserQuestion", "Veuillez remplir tous les champs");
                return;
            } else if (strlen($nomSection) > 50) {
                static::error("afficherFormulairePoserQuestion", "Le nom de la section ne doit pas dépasser 50 caractères");
                return;
            } else if (strlen($descriptionSection) > 2000) {
                static::error("afficherFormulairePoserQuestion", "La description de la section ne doit pas dépasser 2000 caractères");
                return;
            }

            $section = new Section(-1, -1, $nomSection, $descriptionSection);
            $sections[] = $section;
            $nbSections++;
        }

        $responsables = [];
        $votants = [];
        foreach ($_POST as $key => $value) {
            if (substr($key, 0, 11) == "responsable" && is_numeric($value)) {
                $idResponsable = intval($value);
                $responsable = Utilisateur::toUtilisateur((new UtilisateurRepository)->select($idResponsable));
                if ($responsable && !in_array($responsable, $responsables)) {
                    $responsables[] = $responsable;
                }
            } else if (substr($key, 0, 6) == "votant" && is_numeric($value)) {
                $idVotant = intval($value);
                $votant = Utilisateur::toUtilisateur((new UtilisateurRepository)->select($idVotant));
                if ($votant && !in_array($votant, $votants)) {
                    $votants[] = $votant;
                }
            }
        }

        foreach (['dateDebutRedaction', 'dateFinRedaction', 'dateOuvertureVotes', 'dateFermetureVotes'] as $date) {
            if (!isset($_POST[$date]) || !preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $_POST[$date])) {
                static::error("afficherFormulairePoserQuestion", "Veuillez entrer des dates valides");
                return;
            }
        }

        foreach (['heureDebutRedaction', 'heureFinRedaction', 'heureOuvertureVotes', 'heureFermetureVotes'] as $heure) {
            if (!isset($_POST[$heure]) || !preg_match("/^[0-9]{2}:[0-9]{2}$/", $_POST[$heure])) {
                static::error("afficherFormulairePoserQuestion", "Veuillez entrer des heures valides");
                return;
            }
        }

        $dateDebutRedaction = DateTime::createFromFormat("Y-m-d H:i", $_POST['dateDebutRedaction'] . " " . $_POST['heureDebutRedaction']);
        $dateFinRedaction = DateTime::createFromFormat("Y-m-d H:i", $_POST['dateFinRedaction'] . " " . $_POST['heureFinRedaction']);
        $dateOuvertureVotes = DateTime::createFromFormat("Y-m-d H:i", $_POST['dateOuvertureVotes'] . " " . $_POST['heureOuvertureVotes']);
        $dateFermetureVotes = DateTime::createFromFormat("Y-m-d H:i", $_POST['dateFermetureVotes'] . " " . $_POST['heureFermetureVotes']);

        $dateCoherentes =
            $dateDebutRedaction < $dateFinRedaction
            && $dateFinRedaction <= $dateOuvertureVotes
            && $dateOuvertureVotes < $dateFermetureVotes
            && $dateDebutRedaction > (new DateTime("now"));

        if (!$dateCoherentes) {
            static::error("afficherFormulairePoserQuestion", "Les dates ne sont pas cohérentes");
            return;
        } else if ($description == "") {
            static::error("afficherFormulairePoserQuestion", "Veuillez remplir tous les champs");
            return;
        } else if (count($sections) == 0) {
            static::error("afficherFormulairePoserQuestion", "Au moins une section est requise");
            return;
        } else if (count($responsables) == 0) {
            static::error("afficherFormulairePoserQuestion", "Veuillez sélectionner au moins un responsable");
            return;
        } else if (count($votants) == 0) {
            static::error("afficherFormulairePoserQuestion", "Veuillez sélectionner au moins un votant");
            return;
        }

        $question->setDescription($description);
        $question->setSections($sections);
        $question->setResponsables($responsables);
        $question->setVotants($votants);
        $question->setDateDebutRedaction($dateDebutRedaction);
        $question->setDateFinRedaction($dateFinRedaction);
        $question->setDateOuvertureVotes($dateOuvertureVotes);
        $question->setDateFermetureVotes($dateFermetureVotes);

        (new QuestionRepository)->updateEbauche($question);
        $_GET['idUtilisateur'] = $question->getOrganisateur()->getIdUtilisateur();
        static::message("listerMesQuestions", "La question a été posée");
    }

    public static function passagePhaseRedaction()
    {

        if (!is_numeric($_GET['idQuestion'])) {
            static::error("afficherAccueil", "Veuillez entrer un identifiant de question valide");
            return;
        }

        $idQuestion = intval($_GET['idQuestion']);
        $question = Question::toQuestion((new QuestionRepository)->select($idQuestion));

        if (!$question) {
            static::error("afficherAccueil", "La question n'existe pas");
            return;
        }

        $phase = $question->getPhase();
        if ($phase != Phase::Attente) {
            static::error("afficherAccueil", "Vous ne pouvez pas passer à la phase de rédaction depuis cette phase");
            return;
        }

        $question->setDateDebutRedaction(new DateTime("now"));
        (new QuestionRepository)->update($question);

        $_GET['idUtilisateur'] = $question->getOrganisateur()->getIdUtilisateur();
        static::message("listerMesQuestions", "La question est maintenant en phase de rédaction");
    }

    public static function passagePhaseVote()
    {
        if (!is_numeric($_GET['idQuestion'])) {
            static::error("afficherAccueil", "Veuillez entrer un identifiant de question valide");
            return;
        }

        $idQuestion = intval($_GET['idQuestion']);
        $question = Question::toQuestion((new QuestionRepository)->select($idQuestion));

        if (!$question) {
            static::error("afficherAccueil", "La question n'existe pas");
            return;
        }

        $phase = $question->getPhase();

        if ($phase != Phase::Redaction && $phase != Phase::Lecture) {
            static::error("afficherAccueil", "La question n'est pas en phase de rédaction ou de vote");
            return;
        }

        if ($phase == Phase::Redaction)
            $question->setDateFinRedaction(new DateTime("now"));

        $question->setDateOuvertureVotes(new DateTime("now"));
        (new QuestionRepository)->update($question);

        $_GET['idUtilisateur'] = $question->getOrganisateur()->getIdUtilisateur();
        static::message("listerMesQuestions", "La question est maintenant en phase de vote");
    }

    public static function listerMesQuestions()
    {
        if (!is_numeric($_GET['idUtilisateur'])) {
            static::error("afficherAccueil", "Veuillez entrer un identifiant d'utilisateur valide");
            return;
        }

        $idUtilisateur = intval($_GET['idUtilisateur']);
        $questions = (new QuestionRepository)->getQuestionsParOrganisateur($idUtilisateur);

        static::afficherVue("view.php", [
            "titrePage" => "Mes questions",
            "contenuPage" => "listeMesQuestions.php",
            "questions" => $questions
        ]);
    }
}
