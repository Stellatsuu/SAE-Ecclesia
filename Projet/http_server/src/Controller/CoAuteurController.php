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
use App\SAE\Model\Repository\CoAuteurRepository;
use App\SAE\Model\Repository\DemandeCoAuteurRepository;

class CoAuteurController extends MainController
{

    public static function afficherFormulaireGererCoAuteurs()
    {
        $session = static::getSessionSiConnecte();
        $username = $session->lire("username");
        
        $idProposition = static::getIfSetAndNumeric("idProposition");
        $proposition = Proposition::castIfNotNull((new PropositionRepository())->select($idProposition));

        if($proposition->getUsernameResponsable() != $username) {
            static::error(LMQ_URL, "Vous n'êtes pas le responsable de cette proposition.");
        }

        $question = $proposition->getQuestion();
        $phase = $question->getPhase();
        switch ($phase) {
            case Phase::Attente:
            case Phase::NonRemplie:
                QuestionController::error(LMQ_URL, "La question n'est pas encore prête. Vous ne pouvez pas encore gérer les co-auteurs.");
                break;
            case Phase::Vote:
                QuestionController::error(LMQ_URL, "La question est en cours de vote. Vous ne pouvez plus gérer les co-auteurs.");
                break;
            case Phase::Resultat:
                QuestionController::error(LMQ_URL, "La question est terminée. Vous ne pouvez plus gérer les co-auteurs.");
                break;
        }

        $utilisateurs = (new UtilisateurRepository())->selectAll();

        $utilisateursAutorises = array_values(array_filter($utilisateurs, function ($utilisateur) use ($proposition) {
            return $utilisateur->getUsername() != $proposition->getUsernameResponsable();
        }));

        $demandesCoAuteur = (new DemandeCoAuteurRepository)->selectAllByProposition($proposition->getIdProposition());

        static::afficherVue('view.php', [
            "titrePage" => "Gérer les co-auteurs",
            "contenuPage" => "formulaireGererCoAuteurs.php",
            "proposition" => $proposition,
            "utilisateursAutorises" => $utilisateursAutorises,
            "coAuteurs" => (new CoAuteurRepository())->selectAllByProposition($proposition->getIdProposition()),
            "demandesCoAuteur" => $demandesCoAuteur
        ]);
    }

    public static function gererCoAuteurs()
    {
        $session = static::getSessionSiConnecte();
        $username = $session->lire("username");

        $idProposition = static::getIfSetAndNumeric("idProposition");
        $proposition = Proposition::castIfNotNull((new PropositionRepository())->select($idProposition));

        if($proposition->getUsernameResponsable() != $username) {
            static::error(LMQ_URL, "Vous n'êtes pas le responsable de cette proposition.");
        }

        $question = $proposition->getQuestion();
        $phase = $question->getPhase();
        switch ($phase) {
            case Phase::Attente:
            case Phase::NonRemplie:
                QuestionController::error(ACCUEIL_URL, "La question n'est pas encore prête. Vous ne pouvez pas encore gérer les co-auteurs.");
                break;
            case Phase::Vote:
                QuestionController::error(ACCUEIL_URL, "La question est en cours de vote. Vous ne pouvez plus gérer les co-auteurs.");
                break;
            case Phase::Resultat:
                QuestionController::error(ACCUEIL_URL, "La question est terminée. Vous ne pouvez plus gérer les co-auteurs.");
                break;
        }

        $coAuteurs = [];
        foreach ($_POST as $key => $value) {
            if (substr($key, 0, 9) == "co_auteur") {
                $usernameCoAuteur = $value;
                $coAuteur = Utilisateur::castIfNotNull((new UtilisateurRepository())->select($usernameCoAuteur));
                if ($coAuteur && !in_array($coAuteur, $coAuteurs)) {
                    $coAuteurs[] = $coAuteur;
                }
            }
        }

        if (in_array($proposition->getResponsable(), $coAuteurs)) {
            static::error("frontController.php?controller=coAuteur&action=afficherFormulaireGererCoAuteurs&idProposition=$idProposition", "Vous ne pouvez pas être co-auteur de votre propre proposition.");
            return;
        }

        (new CoAuteurRepository)->deleteAllByProposition($proposition->getIdProposition());
        foreach ($coAuteurs as $coAuteur) {
            (new CoAuteurRepository)->insertGlobal($proposition->getIdProposition(), $coAuteur->getUsername());
        }

        static::message(LMQ_URL, "Les co-auteurs ont bien été modifiés.");
    }

    public static function afficherFormulaireDemanderCoAuteur()
    {
        $session = static::getSessionSiConnecte();
        $username = $session->lire("username");

        $idProposition = static::getIfSetAndNumeric("idProposition");

        $proposition = Proposition::castIfNotNull((new PropositionRepository())->select($idProposition));

        $estLieAQuestion = (new UtilisateurRepository())->estLieAQuestion($username, $proposition->getIdQuestion());
        if(!$estLieAQuestion) {
            static::error(LMQ_URL, "Vous n'avez pas les droits pour demander à être co-auteur de cette proposition.");
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
        $session = static::getSessionSiConnecte();

        $username = $session->lire('username');
        $utilisateur = Utilisateur::castIfNotNull((new UtilisateurRepository())->select($username));

        $idProposition = static::getIfSetAndNumeric("idProposition");
        $proposition = Proposition::castIfNotNull((new PropositionRepository())->select($idProposition));

        $coAuteurs = (new CoAuteurRepository)->selectAllByProposition($proposition->getIdProposition());
        if (in_array($utilisateur, $coAuteurs)) {
            static::error(ACCUEIL_URL, "Vous êtes déjà co-auteur de cette proposition.");
        }

        if ($proposition->getUsernameResponsable() == $username) {
            static::error(ACCUEIL_URL, "Vous êtes le responsable de cette proposition.");
        }

        $exists = (new DemandeCoAuteurRepository)->select($username, $idProposition);
        if ($exists) {
            static::error(ACCUEIL_URL, "Vous avez déjà demandé à être co-auteur de cette proposition.");
        }

        $demandeCoAuteur = new DemandeCoAuteur($username, $idProposition, $message);
        (new DemandeCoAuteurRepository)->insert($demandeCoAuteur);
        static::message(ACCUEIL_URL, "Votre demande a bien été envoyée.");
    }

    public static function accepterDemandeCoAuteur()
    {
        $session = static::getSessionSiConnecte();
        $username = $session->lire('username');

        $usernameDemandeur = static::getIfSet("usernameDemandeur");
        $demandeur = Utilisateur::castIfNotNull((new UtilisateurRepository())->select($usernameDemandeur));

        $idProposition = static::getIfSetAndNumeric("idProposition");
        $proposition = Proposition::castIfNotNull((new PropositionRepository())->select($idProposition));

        if ($proposition->getUsernameResponsable() != $username) {
            static::error(ACCUEIL_URL, "Vous n'êtes pas le responsable de cette proposition.");
        }

        $demandeCoAuteur = DemandeCoAuteur::castIfNotNull((new DemandeCoAuteurRepository)->select($usernameDemandeur, $idProposition));

        (new DemandeCoAuteurRepository)->delete($usernameDemandeur, $idProposition);
        (new CoAuteurRepository)->insertGlobal($idProposition, $usernameDemandeur);
        static::message("frontController.php?controller=coAuteur&action=afficherFormulaireGererCoAuteurs&idProposition=$idProposition", "La demande de co-auteur a bien été acceptée.");
    }

    public static function refuserDemandeCoAuteur() {
        $session = static::getSessionSiConnecte();
        $username = $session->lire('username');

        $usernameDemandeur = static::getIfSet("usernameDemandeur");
        $demandeur = Utilisateur::castIfNotNull((new UtilisateurRepository())->select($usernameDemandeur));

        $idProposition = static::getIfSetAndNumeric("idProposition");
        $proposition = Proposition::castIfNotNull((new PropositionRepository())->select($idProposition));

        if ($proposition->getUsernameResponsable() != $username) {
            static::error(ACCUEIL_URL, "Vous n'êtes pas le responsable de cette proposition.");
        }

        $demandeCoAuteur = DemandeCoAuteur::castIfNotNull((new DemandeCoAuteurRepository)->select($usernameDemandeur, $idProposition));

        (new DemandeCoAuteurRepository)->delete($usernameDemandeur, $idProposition);
        static::message("frontController.php?controller=coAuteur&action=afficherFormulaireGererCoAuteurs&idProposition=$idProposition", "La demande de co-auteur a bien été refusée.");
    }
}
