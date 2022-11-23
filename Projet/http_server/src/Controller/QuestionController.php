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

class QuestionController extends Controller
{

    public static function afficherFormulairePoserQuestion(): void
    {
        $idQuestion = intval($_GET['idQuestion']);
        $question = Question::toQuestion((new QuestionRepository)->select($idQuestion));
        $utilisateurs = (new UtilisateurRepository)->selectAll();

        $phase = $question->getPhase();

        if ($phase !== Phase::NonRemplie && $phase !== Phase::Attente) {
            static::error("afficherAccueil", "La question est déjà en cours de rédaction ou de vote, elle ne peut plus être modifiée.");
            return;
        }

        if ($phase === Phase::NonRemplie) {
            $question->setDateDebutRedaction((new DateTime())->add(new DateInterval('P1D'))->setTime(16, 0, 0));
            $question->setDateFinRedaction((new DateTime())->add(new DateInterval('P8D'))->setTime(16, 0, 0));
            $question->setDateOuvertureVotes((new DateTime())->add(new DateInterval('P8D'))->setTime(16, 0, 0));
            $question->setDateFermetureVotes((new DateTime())->add(new DateInterval('P15D'))->setTime(16, 0, 0));
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
        $idQuestion = intval($_POST['idQuestion']);
        $description = $_POST['description'];

        $questionOld = Question::toQuestion((new QuestionRepository)->select($idQuestion));

        $phase = $questionOld->getPhase();
        if ($phase !== Phase::NonRemplie && $phase !== Phase::Attente) {
            static::error("afficherAccueil", "La question est déjà en cours de rédaction ou de vote, elle ne peut plus être modifiée.");
            return;
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
            }

            if (strlen($nomSection) > 50) {
                static::error("afficherFormulairePoserQuestion", "Le nom de la section ne doit pas dépasser 50 caractères");
                return;
            }

            if (strlen($descriptionSection) > 2000) {
                static::error("afficherFormulairePoserQuestion", "La description de la section ne doit pas dépasser 2000 caractères");
                return;
            }

            $section = new Section(-1, -1, $nomSection, $descriptionSection);
            $sections[] = $section;
            $nbSections++;
        }

        if (count($sections) == 0) {
            static::error("afficherFormulairePoserQuestion", "Au moins une section est requise");
            return;
        }

        $nbResponsables = 1;
        $responsables = [];
        while (isset($_POST['responsable' . $nbResponsables])) {
            $idResponsable = intval($_POST['responsable' . $nbResponsables]);
            $responsable = Utilisateur::toUtilisateur((new UtilisateurRepository)->select($idResponsable));
            if ($responsable && !in_array($responsable, $responsables)) {
                $responsables[] = $responsable;
            }
            $nbResponsables++;
        }

        if (count($responsables) == 0) {
            static::error("afficherFormulairePoserQuestion", "Veuillez sélectionner au moins un responsable");
            return;
        }

        $nbVotants = 1;
        $votants = [];
        while (isset($_POST['votant' . $nbVotants])) {
            $idVotant = intval($_POST['votant' . $nbVotants]);
            $votant = Utilisateur::toUtilisateur((new UtilisateurRepository)->select($idVotant));
            if ($votant && !in_array($votant, $votants)) {
                $votants[] = $votant;
            }
            $nbVotants++;
        }

        if (count($votants) == 0) {
            static::error("afficherFormulairePoserQuestion", "Veuillez sélectionner au moins un votant");
            return;
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

        if ($description == "") {
            static::error("afficherFormulairePoserQuestion", "Veuillez remplir tous les champs");
            return;
        }

        $question = new Question(
            $idQuestion,
            $questionOld->getTitre(),
            $description,
            $questionOld->getOrganisateur(),
            $sections,
            $responsables,
            $votants,
            $dateDebutRedaction,
            $dateFinRedaction,
            $dateOuvertureVotes,
            $dateFermetureVotes
        );

        (new QuestionRepository)->updateEbauche($question);

        $_GET['idUtilisateur'] = $questionOld->getOrganisateur()->getIdUtilisateur();
        static::message("listerMesQuestions", "La question a été posée");
    }

    public static function passagePhaseVote()
    {
        $idQuestion = intval($_GET['idQuestion']);
        $question = Question::toQuestion((new QuestionRepository)->select($idQuestion));

        $phase = $question->getPhase();
        if ($phase != Phase::Redaction && $phase != Phase::Lecture) {
            static::error("afficherAccueil", "Vous ne pouvez pas passer à la phase de vote depuis cette phase");
            return;
        }

        if ($phase == Phase::Redaction)
            $question->setDateFinRedaction(new DateTime("now"));

        $question->setDateOuvertureVotes(new DateTime("now"));
        (new QuestionRepository)->update($question);

        $_GET['idUtilisateur'] = $question->getOrganisateur()->getIdUtilisateur();
        static::message("listerMesQuestions", "La question est maintenant en phase de vote");
    }

    public static function passagePhaseRedaction()
    {
        $idQuestion = intval($_GET['idQuestion']);
        $question = Question::toQuestion((new QuestionRepository)->select($idQuestion));

        $phase = $question->getPhase();
        if ($phase != Phase::Attente) {
            static::error("afficherAccueil", "Vous ne pouvez pas passer à la phase de rédaction depuis cette phase");
            return;
        }

        $question->setDateDebutRedaction(new DateTime("now"));
        (new QuestionRepository)->update($question);

        $_GET['idUtilisateur'] = $question->getOrganisateur()->getIdUtilisateur();
        static::message("afficherMesQuestions", "La question est maintenant en phase de rédaction");
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
