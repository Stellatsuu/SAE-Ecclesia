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

class PropositionController extends MainController
{

    public static function afficherFormulaireEcrireProposition()
    {

        /* TODO: vérifier si l'utilisateur est co-auteur ou rédacteur (si co-auteur, pas le droit de toucher au titre) --> need authentification*/

        if (!isset($_GET['idQuestion']) || !is_numeric($_GET['idQuestion'])) {
            static::error("frontController.php", "Aucune question n'a été sélectionnée");
            return;
        }

        $idQuestion = $_GET['idQuestion'];

        $question = (new QuestionRepository())->select($idQuestion);
        if (!$question) {
            static::error("frontController.php", "La question n'existe pas");
            return;
        }

        $question = Question::castIfNotNull($question);
        $phase = $question->getPhase();
        if ($phase !== Phase::Redaction) {
            switch ($phase) {
                case Phase::Attente:
                case Phase::NonRemplie:
                    QuestionController::error("frontController.php", "La question n'est pas encore prête. Vous ne pouvez pas encore écrire de proposition.");
                    break;
                case Phase::Vote:
                    QuestionController::error("frontController.php", "La question est en cours de vote. Vous ne pouvez plus écrire de proposition.");
                    break;
                case Phase::Resultat:
                    QuestionController::error("frontController.php", "La question est terminée. Vous ne pouvez plus écrire de proposition.");
                    break;
            }
            return;
        }

        static::afficherVue("view.php", [
            "question" => $question,
            "titrePage" => "Écrire une proposition",
            "contenuPage" => "formulaireEcrireProposition.php"
        ]);
    }

    public static function ecrireProposition()
    {
        if (!isset($_POST['idQuestion']) || !is_numeric($_POST['idQuestion'])) {
            static::error("frontController.php", "Veuillez sélectionner la question pour laquelle vous souhaitez écrire une proposition.");
            return;
        }

        $question = Question::castIfNotNull((new QuestionRepository())->select($_POST['idQuestion']));

        if (!$question) {
            static::error("frontController.php", "La question n'existe pas");
            return;
        } else if (!isset($_POST['titreProposition'])) {
            static::error("frontController.php", "Veuillez saisir un titre pour votre proposition.");
            return;
        }
        $titreProposition = $_POST['titreProposition'];
        $idResponsable = $_POST['idResponsable'];

        $responsable = (new UtilisateurRepository())->select($idResponsable);

        if (!$responsable) {
            static::error("frontController.php", "Le responsable n'existe pas");
            return;
        }

        $phase = $question->getPhase();
        switch ($phase) {
            case Phase::Attente:
            case Phase::NonRemplie:
                QuestionController::error("afficherAccueil", "La question n'est pas encore prête. Vous ne pouvez pas encore écrire de proposition.");
                break;
            case Phase::Vote:
                QuestionController::error("afficherAccueil", "La question est en cours de vote. Vous ne pouvez plus écrire de proposition.");
                break;
            case Phase::Resultat:
                QuestionController::error("afficherAccueil", "La question est terminée. Vous ne pouvez plus écrire de proposition.");
                break;
        }

        if ($titreProposition == "") {
            static::error("frontController.php", "Veuillez saisir un titre pour votre proposition.");
            return;
        } else if (strlen($titreProposition) > 100) {
            static::error("frontController.php", "Le titre de votre proposition ne doit pas dépasser 100 caractères.");
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
            if (!isset($_POST[$nom_paragraphe]) || $_POST[$nom_paragraphe] == "") {
                static::error("frontController.php", "Veuillez saisir un contenu pour votre proposition.");
                return;
            }

            $contenu = $_POST[$nom_paragraphe];

            $paragraphe = new Paragraphe(
                -1,
                $proposition->getIdProposition(),
                $sections[$i],
                $contenu
            );
            $paragraphes[] = $paragraphe;
        }

        $proposition->setParagraphes($paragraphes);

        $estRedacteur = (new QuestionRepository())->estRedacteur($question->getIdQuestion(), $idResponsable);
        if (!$estRedacteur) {
            static::error("frontController.php", "Vous ne faites pas partie des rédacteurs de cette question.");
            return;
        }

        $propositionExiste = (new PropositionRepository())->selectByQuestionEtRedacteur($question->getIdQuestion(), $idResponsable) == null ? false : true;
        if ($propositionExiste) {
            static::error("frontController.php", "Vous avez déjà écrit une proposition pour cette question.");
            return;
        }

        (new PropositionRepository())->insert($proposition);

        static::message("frontController.php?controller=question&action=listerMesQuestions", "La proposition a bien été enregistrée.");
    }

    public static function afficherFormulaireContribuerProposition()
    {
        if (!isset($_GET['idProposition']) || !is_numeric($_GET['idProposition'])) {
            static::error("frontController.php", "Aucune proposition n'a été sélectionnée");
            return;
        }

        $idProposition = $_GET['idProposition'];

        $proposition = (new PropositionRepository())->select($idProposition);
        if (!$proposition) {
            static::error("frontController.php", "La proposition n'existe pas");
            return;
        }
        $proposition = Proposition::castIfNotNull($proposition);

        $phase = $proposition->getQuestion()->getPhase();
        if ($phase !== Phase::Redaction) {
            switch ($phase) {
                case Phase::Attente:
                case Phase::NonRemplie:
                    QuestionController::error("frontController.php", "La question n'est pas encore prête. Vous ne pouvez pas encore contribuer à une proposition.");
                    break;
                case Phase::Vote:
                    QuestionController::error("frontController.php", "La question est en cours de vote. Vous ne pouvez plus contribuer à une proposition.");
                    break;
                case Phase::Resultat:
                    QuestionController::error("frontController.php", "La question est terminée. Vous ne pouvez plus contribuer à une proposition.");
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
        if (!isset($_POST['idProposition']) || !is_numeric($_POST['idProposition'])) {
            static::error("frontController.php?controller=question&action=listerMesQuestions", "Veuillez sélectionner la proposition pour laquelle vous souhaitez contribuer");
            return;
        }

        $proposition = Proposition::castIfNotNull((new PropositionRepository())->select($_POST['idProposition']));

        if (!$proposition) {
            static::error("frontController.php", "La proposition n'existe pas");
            return;
        } else if (!isset($_POST['titreProposition'])) {
            static::error("frontController.php", "Veuillez saisir un titre pour votre proposition.");
        }

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
        $estRedacteur = (new QuestionRepository())->estRedacteur($proposition->getQuestion()->getIdQuestion(), $_POST['idCoAuteur']);
        $estCoAuteur = $estRedacteur; // si l'utilisateur est rédacteur, il a les droits d'édition

        for ($i = 0; $i < count($sections); $i++) {

            $nom_paragraphe = 'section_' . $i;
            if (!isset($_POST[$nom_paragraphe]) || $_POST[$nom_paragraphe] == "") {
                static::error("frontController.php", "Veuillez saisir un contenu pour votre proposition.");
                return;
            }

            $contenu = $_POST[$nom_paragraphe];

            $paragraphe = (new ParagrapheRepository())->selectByPropositionEtSection($proposition->getIdProposition(), $sections[$i]->getIdSection());
            $paragraphe->setContenuParagraphe($contenu);
            $paragraphes[] = $paragraphe;

            if ((new ParagrapheRepository())->estCoAuteur($paragraphe->getIdParagraphe(), $_POST['idCoAuteur'])) {
                $estCoAuteur = true;
            }
        }

        if (!$estCoAuteur) {
            static::error("frontController.php?controller=question&action=listerMesQuestions", "Vous ne faites pas partie des co-auteurs ou des rédacteurs de cette proposition.");
            return;
        }

        if ($estRedacteur) {
            $proposition->setTitreProposition($_POST['titreProposition']);
            (new PropositionRepository())->update($proposition);
        }

        foreach ($paragraphes as $paragraphe) {
            (new ParagrapheRepository())->update($paragraphe);
        }
        static::message("frontController.php?controller=question&action=listerMesQuestions", "La proposition a bien été enregistrée.");
    }

    public static function afficherPropositions()
    {
        $session = Session::getInstance();

        //Vérification si une question est contenue dans l'URL
        if (!isset($_GET['idQuestion']) || !is_numeric($_GET['idQuestion'])) {
            static::error("frontController.php?controller=question&action=listerMesQuestions", "Aucune question n'a été sélectionnée");
            return;
        }

        $idQuestion = $_GET['idQuestion'];

        //Vérification si la question existe
        $question = (new QuestionRepository())->select($idQuestion);
        if (!$question) {
            static::error("frontController.php?controller=question&action=listerMesQuestions", "La question n'existe pas");
            return;
        }

        $question = Question::castIfNotNull($question);


        if (!$session->contient("idUtilisateur")) {
            static::error("frontController.php?controller=question&action=listerMesQuestions", "Vous devez être identifié");
            return;
        }

        $idUtilisateur = $session->lire("idUtilisateur");
        if (!(((new QuestionRepository)->estCoAuteur($idQuestion, $idUtilisateur) || (new QuestionRepository)->estVotant($idQuestion, $idUtilisateur) || ((new QuestionRepository)->estRedacteur($idQuestion, $idUtilisateur)) || ($question->getOrganisateur()->getIdUtilisateur() == $idUtilisateur)))) {
            static::error("frontController.php?controller=question&action=listerMesQuestions", "Vous n'avez pas accès aux propositions");
            return;
        }

        //Vérification si la question contient des propositions
        $propositions = (new PropositionRepository())->selectAllByQuestion($idQuestion);
        if (count($propositions) == 0) {
            static::error("frontController.php?controller=question&action=listerMesQuestions", "Il n'y a aucune proposition pour cette question");
            return;
        }

        //Index pour le tableau de propositions (prop1 = index0)
        if (!isset($_GET['index'])) {
            $index = 0;
        } else {
            $index = $_GET['index'];
        }

        static::afficherVue("view.php", [
            "titrePage" => "Propositions",
            "contenuPage" => "afficherPropositions.php",
            "idQuestion" => $idQuestion,
            "question" => $question,
            "propositions" => $propositions,
            "index" => $index
        ]);
    }

    public static function supprimerProposition()
    {
        $idProposition = static::getIfSetAndNumeric("idProposition");

        $proposition = Proposition::castIfNotNull((new PropositionRepository())->select($idProposition));
        if (!$proposition) {
            static::error("frontController.php", "La proposition n'existe pas.");
            return;
        }

        $question = $proposition->getQuestion();
        $phase = $question->getPhase();
        switch ($phase) {
            case Phase::Attente:
            case Phase::NonRemplie:
                QuestionController::error("frontController.php", "La question n'est pas encore prête. Vous ne pouvez pas encore supprimer de proposition.");
                break;
            case Phase::Vote:
                QuestionController::error("frontController.php", "La question est en cours de vote. Vous ne pouvez plus supprimer de proposition.");
                break;
            case Phase::Resultat:
                QuestionController::error("frontController.php", "La question est terminée. Vous ne pouvez plus supprimer de proposition.");
                break;
        }

        $session = Session::getInstance();
        $idUtilisateur = $session->lire("idUtilisateur");
        if(!($question->getOrganisateur()->getIdUtilisateur() == $idUtilisateur||(new QuestionRepository)->estRedacteur($question->getIdQuestion(), $idUtilisateur))){
            static::error("frontController.php?controller=proposition&action=afficherPropositions".$question->getValeurClePrimaire(), "Vous n'avez pas accès aux propositions");
            return;
        }


        (new PropositionRepository)->deleteCoAuteurs($idProposition);
        $idPragraphes = (new ParagrapheRepository())->selectAllByProposition($idProposition);
        foreach ($idPragraphes as $paragraphe){
            (new ParagrapheRepository())->delete($paragraphe->getIdParagraphe());
        }
        (new PropositionRepository)->delete($idProposition);
        static::message("frontController.php?controller=proposition&action=afficherPropositions&idQuestion=".$question->getValeurClePrimaire(), "La proposition a bien été supprimé.");

    }
}
