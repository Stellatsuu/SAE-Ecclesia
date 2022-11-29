<?php

namespace App\SAE\Controller;

use App\SAE\Model\DataObject\Paragraphe;
use App\SAE\Model\DataObject\Proposition;
use App\SAE\Model\DataObject\Question;
use App\SAE\Model\DataObject\Utilisateur;
use App\SAE\Model\HTTP\Session;
use App\SAE\Model\Repository\ParagrapheRepository;
use App\SAE\Model\Repository\PropositionRepository;
use App\SAE\Model\Repository\QuestionRepository;
use App\SAE\Model\Repository\UtilisateurRepository;
use App\SAE\Lib\PhaseQuestion as Phase;
use App\SAE\Model\DataObject\DemandeCoAuteur;
use App\SAE\Model\Repository\DemandeCoAuteurRepository;

class CoAuteurController extends MainController
{

    public static function afficherFormulaireGererCoAuteurs()
    {
        if (!isset($_GET['idProposition']) || !is_numeric($_GET['idProposition'])) {
            static::error("frontController.php", "Aucune proposition n'a été sélectionnée.");
            return;
        }
        $idProposition = intval($_GET['idProposition']);

        $proposition = (new PropositionRepository())->select($idProposition);
        if (!$proposition) {
            static::error("frontController.php?controller=question&action=listerMesQuestions", "La proposition n'existe pas.");
            return;
        }
        $proposition = Proposition::castIfNotNull($proposition);

        $question = $proposition->getQuestion();
        $phase = $question->getPhase();
        switch ($phase) {
            case Phase::Attente:
            case Phase::NonRemplie:
                QuestionController::error("frontController.php?controller=question&action=listerMesQuestions", "La question n'est pas encore prête. Vous ne pouvez pas encore gérer les co-auteurs.");
                break;
            case Phase::Vote:
                QuestionController::error("frontController.php?controller=question&action=listerMesQuestions", "La question est en cours de vote. Vous ne pouvez plus gérer les co-auteurs.");
                break;
            case Phase::Resultat:
                QuestionController::error("frontController.php?controller=question&action=listerMesQuestions", "La question est terminée. Vous ne pouvez plus gérer les co-auteurs.");
                break;
        }

        $utilisateurs = (new UtilisateurRepository())->selectAll();

        $utilisateursAutorises = array_values(array_filter($utilisateurs, function ($utilisateur) use ($proposition) {
            return $utilisateur->getIdUtilisateur() != $proposition->getResponsable()->getIdUtilisateur();
        }));

        $demandesCoAuteur = (new DemandeCoAuteurRepository)->selectAllByProposition($proposition->getIdProposition());

        static::afficherVue('view.php', [
            "titrePage" => "Gérer les co-auteurs",
            "contenuPage" => "formulaireGererCoAuteurs.php",
            "proposition" => $proposition,
            "utilisateursAutorises" => $utilisateursAutorises,
            "coAuteurs" => $proposition->getCoAuteurs(),
            "demandesCoAuteur" => $demandesCoAuteur
        ]);
    }

    public static function gererCoAuteurs()
    {
        if (!isset($_POST['idProposition']) || !is_numeric($_POST['idProposition'])) {
            static::error("frontController.php", "Aucune proposition n'a été sélectionnée.");
            return;
        }
        $idProposition = intval($_POST['idProposition']);

        $proposition = (new PropositionRepository())->select($idProposition);
        if (!$proposition) {
            static::error("frontController.php", "La proposition n'existe pas.");
            return;
        }
        $proposition = Proposition::castIfNotNull($proposition);

        $question = $proposition->getQuestion();
        $phase = $question->getPhase();
        switch ($phase) {
            case Phase::Attente:
            case Phase::NonRemplie:
                QuestionController::error("frontController.php", "La question n'est pas encore prête. Vous ne pouvez pas encore gérer les co-auteurs.");
                break;
            case Phase::Vote:
                QuestionController::error("frontController.php", "La question est en cours de vote. Vous ne pouvez plus gérer les co-auteurs.");
                break;
            case Phase::Resultat:
                QuestionController::error("frontController.php", "La question est terminée. Vous ne pouvez plus gérer les co-auteurs.");
                break;
        }

        $coAuteurs = [];
        foreach ($_POST as $key => $value) {
            if (substr($key, 0, 9) == "co_auteur") {
                $idCoAuteur = intval($value);
                $coAuteur = Utilisateur::castIfNotNull((new UtilisateurRepository())->select($idCoAuteur));
                if ($coAuteur && !in_array($coAuteur, $coAuteurs)) {
                    $coAuteurs[] = $coAuteur;
                }
            }
        }

        if (in_array($proposition->getResponsable(), $coAuteurs)) {
            static::error("frontController.php?controller=coAuteur&action=afficherFormulaireGererCoAuteurs&idProposition=$idProposition", "Vous ne pouvez pas être co-auteur de votre propre proposition.");
            return;
        }

        (new PropositionRepository)->deleteCoAuteurs($proposition->getIdProposition());
        foreach ($coAuteurs as $coAuteur) {
            (new PropositionRepository)->addCoAuteurGlobal($proposition->getIdProposition(), $coAuteur->getIdUtilisateur());
        }

        static::message("frontController.php?controller=question&action=listerMesQuestions", "Les co-auteurs ont bien été modifiés.");
    }

    public static function afficherFormulaireDemanderCoAuteur()
    {
        if (!isset($_GET['idProposition']) || !is_numeric($_GET['idProposition'])) {
            static::error("frontController.php", "Aucune proposition n'a été sélectionnée.");
            return;
        }
        $idProposition = intval($_GET['idProposition']);

        $proposition = Proposition::castIfNotNull((new PropositionRepository())->select($idProposition));
        if (!$proposition) {
            static::error("frontController.php", "La proposition n'existe pas.");
            return;
        }

        static::afficherVue('view.php', [
            "titrePage" => "Demander à être co-auteur",
            "contenuPage" => "formulaireDemanderCoAuteur.php",
            "proposition" => $proposition
        ]);
    }

    public static function demanderCoAuteur()
    {
        $message = isset($_POST['message']) ? $_POST['message'] : "";
        $session = Session::getInstance();

        if (!$session->contient('idUtilisateur')) {
            static::error("frontController.php", "Vous devez être connecté pour demander à être co-auteur.");
            return;
        }

        $idUtilisateur = $session->lire('idUtilisateur');
        if (!isset($_POST['idProposition']) || !is_numeric($_POST['idProposition'])) {
            static::error("frontController.php", "Aucune proposition n'a été sélectionnée.");
            return;
        }

        $utilisateur = (new UtilisateurRepository())->select($idUtilisateur);
        if (!$utilisateur) {
            static::error("frontController.php", "L'utilisateur n'existe pas.");
            return;
        }

        $idProposition = intval($_POST['idProposition']);
        $proposition = Proposition::castIfNotNull((new PropositionRepository())->select($idProposition));
        if (!$proposition) {
            static::error("frontController.php", "La proposition n'existe pas.");
            return;
        }

        $coAuteurs = (new PropositionRepository)->selectCoAuteurs($proposition->getIdProposition());
        if (in_array($utilisateur, $coAuteurs)) {
            static::error("frontController.php", "Vous êtes déjà co-auteur de cette proposition.");
            return;
        }

        if ($proposition->getResponsable()->getIdUtilisateur() == $idUtilisateur) {
            static::error("frontController.php", "Vous êtes déjà responsable de cette proposition.");
            return;
        }

        $exists = (new DemandeCoAuteurRepository)->select($idUtilisateur, $idProposition);
        if ($exists) {
            static::error("frontController.php", "Vous avez déjà demandé à être co-auteur de cette proposition.");
            return;
        }

        $demandeCoAuteur = new DemandeCoAuteur($idUtilisateur, $idProposition, $message);
        (new DemandeCoAuteurRepository)->insert($demandeCoAuteur);
        static::message("frontController.php", "Votre demande a bien été envoyée.");
    }

    public static function accepterDemandeCoAuteur()
    {
        $session = Session::getInstance();

        if (!$session->contient('idUtilisateur')) {
            static::error("frontController.php", "Vous devez être connecté pour accepter une demande de co-auteur.");
            return;
        }
        $idUtilisateur = $session->lire('idUtilisateur');

        if (!isset($_GET['idProposition']) || !is_numeric($_GET['idProposition'])) {
            static::error("frontController.php", "Aucune proposition n'a été sélectionnée.");
            return;
        }
        $idProposition = intval($_GET['idProposition']);

        if(!isset($_GET['idDemandeur']) || !is_numeric($_GET['idDemandeur'])) {
            static::error("frontController.php", "Aucun demandeur n'a été sélectionné.");
            return;
        }
        $idDemandeur = intval($_GET['idDemandeur']);

        $demandeur = (new UtilisateurRepository())->select($idDemandeur);
        if (!$demandeur) {
            static::error("frontController.php", "Le demandeur n'existe pas.");
            return;
        }

        $proposition = Proposition::castIfNotNull((new PropositionRepository())->select($idProposition));
        if (!$proposition) {
            static::error("frontController.php", "La proposition n'existe pas.");
            return;
        }

        if ($proposition->getResponsable()->getIdUtilisateur() != $idUtilisateur) {
            static::error("frontController.php", "Vous n'êtes pas le responsable de cette proposition.");
            return;
        }

        $demandeCoAuteur = (new DemandeCoAuteurRepository)->select($idDemandeur, $idProposition);
        if (!$demandeCoAuteur) {
            static::error("frontController.php", "La demande de co-auteur n'existe pas.");
            return;
        }

        (new DemandeCoAuteurRepository)->delete($idDemandeur, $idProposition);
        (new PropositionRepository)->addCoAuteurGlobal($idProposition, $idDemandeur);
        static::message("frontController.php?controller=coAuteur&action=afficherFormulaireGererCoAuteurs&idProposition=$idProposition", "La demande de co-auteur a bien été acceptée.");
    }

    public static function refuserDemandeCoAuteur() {
        $session = Session::getInstance();

        if (!$session->contient('idUtilisateur')) {
            static::error("frontController.php", "Vous devez être connecté pour refuser une demande de co-auteur.");
            return;
        }
        $idUtilisateur = $session->lire('idUtilisateur');

        if (!isset($_GET['idProposition']) || !is_numeric($_GET['idProposition'])) {
            static::error("frontController.php", "Aucune proposition n'a été sélectionnée.");
            return;
        }
        $idProposition = intval($_GET['idProposition']);

        if(!isset($_GET['idDemandeur']) || !is_numeric($_GET['idDemandeur'])) {
            static::error("frontController.php", "Aucun demandeur n'a été sélectionné.");
            return;
        }
        $idDemandeur = intval($_GET['idDemandeur']);

        $demandeur = (new UtilisateurRepository())->select($idDemandeur);
        if (!$demandeur) {
            static::error("frontController.php", "Le demandeur n'existe pas.");
            return;
        }

        $proposition = Proposition::castIfNotNull((new PropositionRepository())->select($idProposition));
        if (!$proposition) {
            static::error("frontController.php", "La proposition n'existe pas.");
            return;
        }

        if ($proposition->getResponsable()->getIdUtilisateur() != $idUtilisateur) {
            static::error("frontController.php", "Vous n'êtes pas le responsable de cette proposition.");
            return;
        }

        $demandeCoAuteur = (new DemandeCoAuteurRepository)->select($idDemandeur, $idProposition);
        if (!$demandeCoAuteur) {
            static::error("frontController.php", "La demande de co-auteur n'existe pas.");
            return;
        }

        (new DemandeCoAuteurRepository)->delete($idDemandeur, $idProposition);
        static::message("frontController.php?controller=coAuteur&action=afficherFormulaireGererCoAuteurs&idProposition=$idProposition", "La demande de co-auteur a bien été refusée.");
    }
}
