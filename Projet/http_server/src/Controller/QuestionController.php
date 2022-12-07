<?php

namespace App\SAE\Controller;

use App\SAE\Model\Repository\QuestionRepository as QuestionRepository;
use App\SAE\Model\DataObject\Question as Question;
use App\SAE\Model\DataObject\Section;
use App\SAE\Model\DataObject\Utilisateur;
use App\SAE\Model\Repository\UtilisateurRepository as UtilisateurRepository;
use App\SAE\Lib\PhaseQuestion as Phase;
use App\SAE\Model\Repository\PropositionRepository as PropositionRepository;
use App\SAE\Model\HTTP\Session;
use DateTime;
use DateInterval;


class QuestionController extends MainController
{

    public static function afficherFormulairePoserQuestion(): void
    {
        $idQuestion = static::getIfSetAndNumeric("idQuestion");
        $question = Question::castIfNotNull((new QuestionRepository)->select($idQuestion));
        $utilisateurs = (new UtilisateurRepository)->selectAll();

        $phase = $question->getPhase();
        switch ($phase) {
            case Phase::Redaction:
                static::error(LMQ_URL, "La question est en phase de rédaction. Vous ne pouvez plus la modifier.");
                return;
                break;
            case Phase::Lecture:
                static::error(LMQ_URL, "La question est en phase de lecture. Vous ne pouvez plus la modifier.");
                return;
                break;
            case Phase::Vote:
                static::error(LMQ_URL, "La question est en phase de vote. Vous ne pouvez plus la modifier.");
                return;
                break;
            case Phase::Resultat:
                static::error(LMQ_URL, "La question est terminée. Vous ne pouvez plus la modifier.");
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
        $idQuestion = static::getIfSetAndNumeric("idQuestion");
        $description = static::getIfSetAndNotEmpty("description");

        $AFPQ_URL = "frontController.php?controller=question&action=afficherFormulairePoserQuestion&idQuestion=$idQuestion";

        $question = Question::castIfNotNull((new QuestionRepository)->select($idQuestion));
        $phase = $question->getPhase();
        switch ($phase) {
            case Phase::Redaction:
                static::error(LMQ_URL, "La question est en phase de rédaction. Vous ne pouvez plus la modifier.");
                return;
                break;
            case Phase::Lecture:
                static::error(LMQ_URL, "La question est en phase de lecture. Vous ne pouvez plus la modifier.");
                return;
                break;
            case Phase::Vote:
                static::error(LMQ_URL, "La question est en phase de vote. Vous ne pouvez plus la modifier.");
                return;
                break;
            case Phase::Resultat:
                static::error(LMQ_URL, "La question est terminée. Vous ne pouvez plus la modifier.");
                return;
                break;
        }

        $nbSections = 1;
        $sections = [];
        while (isset($_POST['nomSection' . $nbSections]) && isset($_POST['descriptionSection' . $nbSections])) {
            $nomSection = static::getIfSetAndNotEmpty("nomSection" . $nbSections, $AFPQ_URL, "Une section doit avoir un nom.");
            $descriptionSection = static::getIfSetAndNotEmpty("descriptionSection" . $nbSections, $AFPQ_URL, "Une section doit avoir une description.");

            if (strlen($nomSection) > 50) {
                static::error($AFPQ_URL, "Le nom de la section ne doit pas dépasser 50 caractères");
                return;
            } else if (strlen($descriptionSection) > 2000) {
                static::error($AFPQ_URL, "La description de la section ne doit pas dépasser 2000 caractères");
                return;
            }

            $section = new Section(-1, -1, $nomSection, $descriptionSection);
            $sections[] = $section;
            $nbSections++;
        }

        $responsables = [];
        $votants = [];
        foreach ($_POST as $key => $value) {
            if (substr($key, 0, 9) == "redacteur" && is_numeric($value)) {
                $idResponsable = intval($value);
                $responsable = Utilisateur::castIfNotNull((new UtilisateurRepository)->select($idResponsable));
                if ($responsable && !in_array($responsable, $responsables)) {
                    $responsables[] = $responsable;
                }
            } else if (substr($key, 0, 6) == "votant" && is_numeric($value)) {
                $idVotant = intval($value);
                $votant = Utilisateur::castIfNotNull((new UtilisateurRepository)->select($idVotant));
                if ($votant && !in_array($votant, $votants)) {
                    $votants[] = $votant;
                }
            }
        }

        foreach (['dateDebutRedaction', 'dateFinRedaction', 'dateOuvertureVotes', 'dateFermetureVotes'] as $date) {
            if (!isset($_POST[$date]) || !preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $_POST[$date])) {
                static::error("frontController.php?controller=question&action=afficherFormulairePoserQuestion&idQuestion=$idQuestion", "Veuillez entrer des dates valides");
                return;
            }
        }

        foreach (['heureDebutRedaction', 'heureFinRedaction', 'heureOuvertureVotes', 'heureFermetureVotes'] as $heure) {
            if (!isset($_POST[$heure]) || !preg_match("/^[0-9]{2}:[0-9]{2}$/", $_POST[$heure])) {
                static::error("frontController.php?controller=question&action=afficherFormulairePoserQuestion&idQuestion=$idQuestion", "Veuillez entrer des heures valides");
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
            static::error("frontController.php?controller=question&action=afficherFormulairePoserQuestion&idQuestion=$idQuestion", "Les dates ne sont pas cohérentes");
            return;
        } else if (strlen($description) > 4000) {
            static::error("frontController.php?controller=question&action=afficherFormulairePoserQuestion&idQuestion=$idQuestion", "La description de la question ne doit pas dépasser 4000 caractères");
            return;
        } else if (count($sections) == 0) {
            static::error("frontController.php?controller=question&action=afficherFormulairePoserQuestion&idQuestion=$idQuestion", "Au moins une section est requise");
            return;
        } else if (count($responsables) == 0) {
            static::error("frontController.php?controller=question&action=afficherFormulairePoserQuestion&idQuestion=$idQuestion", "Veuillez sélectionner au moins un responsable");
            return;
        } else if (count($votants) == 0) {
            static::error("frontController.php?controller=question&action=afficherFormulairePoserQuestion&idQuestion=$idQuestion", "Veuillez sélectionner au moins un votant");
            return;
        }

        $question->setDescription($description);
        $question->setSections($sections);
        $question->setRedacteurs($responsables);
        $question->setVotants($votants);
        $question->setDateDebutRedaction($dateDebutRedaction);
        $question->setDateFinRedaction($dateFinRedaction);
        $question->setDateOuvertureVotes($dateOuvertureVotes);
        $question->setDateFermetureVotes($dateFermetureVotes);

        (new QuestionRepository)->update($question);
        static::message(LMQ_URL, "La question a été posée");
    }

    public static function passagePhaseRedaction()
    {
        $idQuestion = static::getIfSetAndNumeric("idQuestion");

        $question = Question::castIfNotNull((new QuestionRepository)->select($idQuestion));

        $phase = $question->getPhase();
        if ($phase != Phase::Attente) {
            static::error(ACCUEIL_URL, "Vous ne pouvez pas passer à la phase de rédaction depuis cette phase");
            return;
        }

        $question->setDateDebutRedaction(new DateTime("now"));
        (new QuestionRepository)->update($question);
        static::message(LMQ_URL, "La question est maintenant en phase de rédaction");
    }

    public static function passagePhaseVote()
    {
        $idQuestion = static::getIfSetAndNumeric("idQuestion");

        $question = Question::castIfNotNull((new QuestionRepository)->select($idQuestion));

        $phase = $question->getPhase();
        if ($phase != Phase::Redaction && $phase != Phase::Lecture) {
            static::error(ACCUEIL_URL, "La question n'est pas en phase de rédaction ou de vote");
            return;
        }

        if ($phase == Phase::Redaction)
            $question->setDateFinRedaction(new DateTime("now"));

        $question->setDateOuvertureVotes(new DateTime("now"));

        (new QuestionRepository)->update($question);
        static::message(LMQ_URL, "La question est maintenant en phase de vote");
    }

    public static function passagePhaseResultats()
    {
        $idQuestion = static::getIfSetAndNumeric("idQuestion");

        $question = Question::castIfNotNull((new QuestionRepository)->select($idQuestion));

        $phase = $question->getPhase();
        if ($phase != Phase::Vote) {
            static::error(ACCUEIL_URL, "La question n'est pas en phase de vote");
            return;
        }

        $question->setDateFermetureVotes(new DateTime("now"));
        (new QuestionRepository)->update($question);
        static::message(LMQ_URL, "La question est terminée. Vous pouvez maintenant voir les résultats");
    }

    public static function listerMesQuestions()
    {
        $session = static::getSessionSiConnecte();
        $idUtilisateur = $session->lire("idUtilisateur");
        $questions = (new QuestionRepository)->selectAllByOrganisateur($idUtilisateur);

        static::afficherVue("view.php", [
            "titrePage" => "Mes questions",
            "contenuPage" => "listeMesQuestions.php",
            "questions" => $questions
        ]);
    }

    public static function afficherResultats()
    {
        $idQuestion = static::getIfSetAndNumeric("idQuestion");

        $question = Question::castIfNotNull((new QuestionRepository)->select($idQuestion));

        $phase = $question->getPhase();
        if ($phase != Phase::Resultat) {
            static::error(ACCUEIL_URL, "La question n'est pas terminée. Vous ne pouvez pas encore voir les résultats");
            return;
        }

        $propositions = (new PropositionRepository())->selectAllByQuestion($idQuestion);
        if (count($propositions) == 0) {
            static::error(ACCUEIL_URL, "Il n'y a aucune proposition pour cette question");
            return;
        }

        $resultats = $question->getSystemeVote()->getResultats();

        $nbTotalVotes = array_sum($resultats);
        $nbTotalVotes = $nbTotalVotes == 0 ? 1 : $nbTotalVotes;

        $idPropositionsGagnantes = array_keys($resultats, max($resultats));

        $propositionsGagnantes = array_reduce($propositions, function ($carry, $item) use ($idPropositionsGagnantes) {
            if (in_array($item->getIdProposition(), $idPropositionsGagnantes))
                $carry[] = $item;
            return $carry;
        }, []);

        //TODO : déterminer quoi faire en cas d'égalité
        $propositionsGagnante = $propositionsGagnantes[0];

        static::afficherVue("view.php", [
            "titrePage" => "Résultats",
            "contenuPage" => "afficherResultats.php",
            "question" => $question,
            "propositions" => $propositions,
            "propositionGagnante" => $propositionsGagnante,
            "resultats" => $resultats,
            "nbTotalVotes" => $nbTotalVotes
        ]);
    }

    public static function afficherQuestionsFinies()
    {
        $questions = (new QuestionRepository())->selectAllFinies();

        static::afficherVue("view.php", [
            "titrePage" => "Résultats",
            "contenuPage" => "listeQuestionsFinies.php",
            "questions" => $questions
        ]);
    }
}
