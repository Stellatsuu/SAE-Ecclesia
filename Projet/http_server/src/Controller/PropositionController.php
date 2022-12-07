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
use App\SAE\Model\Repository\RedacteurRepository;
use App\SAE\Model\Repository\VotantRepository;

class PropositionController extends MainController
{

    public static function afficherFormulaireEcrireProposition()
    {

        /* TODO: vérifier si l'utilisateur est co-auteur ou rédacteur (si co-auteur, pas le droit de toucher au titre) --> need authentification*/

        $idQuestion = static::getIfSetAndNumeric("idQuestion");

        $question = Question::castIfNotNull((new QuestionRepository)->select($idQuestion));

        $phase = $question->getPhase();
        if ($phase !== Phase::Redaction) {
            switch ($phase) {
                case Phase::Attente:
                case Phase::NonRemplie:
                    QuestionController::error(ACCUEIL_URL, "La question n'est pas encore prête. Vous ne pouvez pas encore écrire de proposition.");
                    break;
                case Phase::Vote:
                    QuestionController::error(ACCUEIL_URL, "La question est en cours de vote. Vous ne pouvez plus écrire de proposition.");
                    break;
                case Phase::Resultat:
                    QuestionController::error(ACCUEIL_URL, "La question est terminée. Vous ne pouvez plus écrire de proposition.");
                    break;
            }
        }

        static::afficherVue("view.php", [
            "question" => $question,
            "titrePage" => "Écrire une proposition",
            "contenuPage" => "formulaireEcrireProposition.php"
        ]);
    }

    public static function ecrireProposition()
    {
        $session = static::getSessionSiConnecte();

        $idQuestion = static::getIfSetAndNumeric("idQuestion");
        $question = Question::castIfNotNull((new QuestionRepository())->select($idQuestion));

        $idResponsable = $session->lire("idUtilisateur");
        $responsable = Utilisateur::castIfNotNull((new UtilisateurRepository())->select($idResponsable));

        $titreProposition = static::getIfSetAndNotEmpty("titreProposition");

        $phase = $question->getPhase();
        switch ($phase) {
            case Phase::Attente:
            case Phase::NonRemplie:
                QuestionController::error(ACCUEIL_URL, "La question n'est pas encore prête. Vous ne pouvez pas encore écrire de proposition.");
                break;
            case Phase::Vote:
                QuestionController::error(ACCUEIL_URL, "La question est en cours de vote. Vous ne pouvez plus écrire de proposition.");
                break;
            case Phase::Resultat:
                QuestionController::error(ACCUEIL_URL, "La question est terminée. Vous ne pouvez plus écrire de proposition.");
                break;
        }

        if (strlen($titreProposition) > 100) {
            static::error(ACCUEIL_URL, "Le titre de votre proposition ne doit pas dépasser 100 caractères.");
            return;
        }

        $proposition = new Proposition(
            -1,
            $titreProposition,
            $responsable,
            $question,
            []
        );

        $paragraphes = [];
        $sections = $question->getSections();
        for ($i = 0; $i < count($sections); $i++) {

            $nom_paragraphe = 'section_' . $i;
            $contenu = static::getIfSetAndNotEmpty($nom_paragraphe, ACCUEIL_URL, "Le contenu de la section " . ($i + 1) . " est vide.");

            $paragraphe = new Paragraphe(
                -1,
                $proposition->getIdProposition(),
                $sections[$i],
                $contenu
            );
            $paragraphes[] = $paragraphe;
        }

        $proposition->setParagraphes($paragraphes);

        $estRedacteur = (new RedacteurRepository())->existeRedacteur($question->getIdQuestion(), $idResponsable);
        if (!$estRedacteur) {
            static::error(ACCUEIL_URL, "Vous ne faites pas partie des rédacteurs de cette question.");
            return;
        }

        $propositionExiste = (new PropositionRepository())->selectByQuestionEtRedacteur($question->getIdQuestion(), $idResponsable) == null ? false : true;
        if ($propositionExiste) {
            static::error(ACCUEIL_URL, "Vous avez déjà écrit une proposition pour cette question.");
            return;
        }

        (new PropositionRepository())->insert($proposition);

        static::message(LMQ_URL, "La proposition a bien été enregistrée.");
    }

    public static function afficherFormulaireContribuerProposition()
    {
        $idProposition = static::getIfSetAndNumeric("idProposition");

        $proposition = Proposition::castIfNotNull((new PropositionRepository())->select($idProposition));

        $phase = $proposition->getQuestion()->getPhase();
        if ($phase !== Phase::Redaction) {
            switch ($phase) {
                case Phase::Attente:
                case Phase::NonRemplie:
                    QuestionController::error(ACCUEIL_URL, "La question n'est pas encore prête. Vous ne pouvez pas encore contribuer à une proposition.");
                    break;
                case Phase::Vote:
                    QuestionController::error(ACCUEIL_URL, "La question est en cours de vote. Vous ne pouvez plus contribuer à une proposition.");
                    break;
                case Phase::Resultat:
                    QuestionController::error(ACCUEIL_URL, "La question est terminée. Vous ne pouvez plus contribuer à une proposition.");
                    break;
            }
            return;
        }

        static::afficherVue("view.php", [
            "proposition" => $proposition,
            "titrePage" => "Écrire une proposition",
            "contenuPage" => "formulaireContribuerProposition.php"
        ]);
    }

    public static function contribuerProposition()
    {
        $session = static::getSessionSiConnecte();
        $idUtilisateur = $session->lire("idUtilisateur");

        $idProposition = static::getIfSetAndNumeric("idProposition");

        $proposition = Proposition::castIfNotNull((new PropositionRepository())->select($idProposition));

        $phase = $proposition->getQuestion()->getPhase();
        switch ($phase) {
            case Phase::Attente:
            case Phase::NonRemplie:
                QuestionController::error("frontController.php?controller=question&action=listerMesQuestions", "La question n'est pas encore prête. Vous ne pouvez pas encore contribuer pour la proposition.");
                break;
            case Phase::Vote:
                QuestionController::error("frontController.php?controller=question&action=listerMesQuestions", "La question est en cours de vote. Vous ne pouvez plus contribuer pour la proposition.");
                break;
            case Phase::Resultat:
                QuestionController::error("frontController.php?controller=question&action=listerMesQuestions", "La question est terminée. Vous ne pouvez plus contribuer pour la proposition.");
                break;
        }

        $paragraphes = [];
        $sections = $proposition->getQuestion()->getSections();
        $estRedacteur = (new RedacteurRepository())->existeRedacteur($proposition->getIdQuestion(), $idUtilisateur);
        $estCoAuteur = $estRedacteur; // si l'utilisateur est rédacteur, il a les droits d'édition

        for ($i = 0; $i < count($sections); $i++) {

            $nom_paragraphe = 'section_' . $i;
            $contenu = static::getIfSetAndNotEmpty($nom_paragraphe, ACCUEIL_URL, "Le contenu de la section " . ($i + 1) . " est vide.");

            $paragraphe = (new ParagrapheRepository())->selectByPropositionEtSection($proposition->getIdProposition(), $sections[$i]->getIdSection());
            $paragraphe->setContenuParagraphe($contenu);
            $paragraphes[] = $paragraphe;

            if ((new ParagrapheRepository())->estCoAuteur($paragraphe->getIdParagraphe(), $idUtilisateur)) {
                $estCoAuteur = true;
            }
        }

        if (!$estCoAuteur) {
            static::error(LMQ_URL, "Vous ne faites pas partie des co-auteurs ou des rédacteurs de cette proposition.");
            return;
        }

        if ($estRedacteur) {
            $proposition->setTitreProposition($_POST['titreProposition']);
            (new PropositionRepository())->update($proposition);
        }

        foreach ($paragraphes as $paragraphe) {
            (new ParagrapheRepository())->update($paragraphe);
        }
        static::message(LMQ_URL, "La proposition a bien été enregistrée.");
    }

    public static function afficherPropositions()
    {
        $session = static::getSessionSiConnecte();
        $idUtilisateur = $session->lire("idUtilisateur");

        $idQuestion = static::getIfSetAndNumeric("idQuestion");

        //Vérification si la question existe
        $question = Question::castIfNotNull((new QuestionRepository())->select($idQuestion));

        $estCoAuteur = (new CoAuteurRepository())->existeCoAuteur($idQuestion, $idUtilisateur);
        $estRedacteur = (new RedacteurRepository())->existeRedacteur($idQuestion, $idUtilisateur);
        $estVotant = (new VotantRepository())->existeVotant($idQuestion, $idUtilisateur);
        $estOrganisateur = $question->getIdOrganisateur() == $idUtilisateur;

        if (!$estCoAuteur && !$estRedacteur && !$estVotant && !$estOrganisateur)
            static::error(LMQ_URL, "Vous n'avez pas accès aux propositions");


        //Vérification si la question contient des propositions
        $propositions = (new PropositionRepository())->selectAllByQuestion($idQuestion);
        if (count($propositions) == 0)
            static::error(LMQ_URL, "Il n'y a aucune proposition pour cette question");


        //Index pour le tableau de propositions (prop1 = index0)
        $index = isset($_GET['index']) ? $_GET['index'] : 0;

        static::afficherVue("view.php", [
            "titrePage" => "Propositions",
            "contenuPage" => "afficherPropositions.php",
            "idQuestion" => $idQuestion,
            "question" => $question,
            "propositions" => $propositions,
            "index" => $index,
            "idUtilisateur" => $idUtilisateur,
        ]);
    }

    public static function supprimerProposition()
    {
        $session = static::getSessionSiConnecte();
        $idUtilisateur = $session->lire("idUtilisateur");

        $idProposition = static::getIfSetAndNumeric("idProposition");

        $proposition = Proposition::castIfNotNull((new PropositionRepository())->select($idProposition));

        $question = $proposition->getQuestion();
        $phase = $question->getPhase();
        switch ($phase) {
            case Phase::Attente:
            case Phase::NonRemplie:
                QuestionController::error(ACCUEIL_URL, "La question n'est pas encore prête. Vous ne pouvez pas encore supprimer de proposition.");
                break;
            case Phase::Vote:
                QuestionController::error(ACCUEIL_URL, "La question est en cours de vote. Vous ne pouvez plus supprimer de proposition.");
                break;
            case Phase::Resultat:
                QuestionController::error(ACCUEIL_URL, "La question est terminée. Vous ne pouvez plus supprimer de proposition.");
                break;
        }

        $estRedacteur = (new RedacteurRepository())->existeRedacteur($question->getIdQuestion(), $idUtilisateur);
        $estOrganisateur = $question->getIdOrganisateur() == $idUtilisateur;

        $AP_URL = "frontController.php?controller=proposition&action=afficherPropositions&idQuestion=" . $question->getIdQuestion();

        if (!$estRedacteur && !$estOrganisateur) {
            
            static::error($AP_URL, "Vous n'avez pas les droits pour supprimer cette proposition");
        }

        /* (new PropositionRepository)->deleteCoAuteurs($idProposition);
        $idPragraphes = (new ParagrapheRepository())->selectAllByProposition($idProposition);
        foreach ($idPragraphes as $paragraphe) {
            (new ParagrapheRepository())->delete($paragraphe->getIdParagraphe());
        } */
        (new PropositionRepository)->delete($idProposition);
        static::message($AP_URL, "La proposition a bien été supprimée.");
    }
}
