<?php

namespace App\SAE\Controller;

use App\SAE\Model\DataObject\Paragraphe;
use App\SAE\Model\DataObject\Proposition;
use App\SAE\Model\DataObject\Question;
use App\SAE\Model\DataObject\Utilisateur;
use App\SAE\Model\Repository\ParagrapheRepository;
use App\SAE\Model\Repository\PropositionRepository;
use App\SAE\Model\Repository\QuestionRepository;
use App\SAE\Model\Repository\UtilisateurRepository;
use App\SAE\Lib\PhaseQuestion as Phase;

class PropositionController extends MainController
{

    public static function afficherFormulaireEcrireProposition()
    {

        /* TODO: vérifier si l'utilisateur est co-auteur ou rédacteur (si co-auteur, pas le droit de toucher au titre) --> need authentification*/

        if (!isset($_GET['idQuestion']) || !is_numeric($_GET['idQuestion'])) {
            static::error("afficherAccueil", "Aucune question n'a été sélectionnée");
            return;
        }

        $idQuestion = $_GET['idQuestion'];

        $question = (new QuestionRepository())->select($idQuestion);
        if (!$question) {
            static::error("afficherAccueil", "La question n'existe pas");
            return;
        }

        $question = Question::toQuestion($question);
        $phase = $question->getPhase();
        if ($phase !== Phase::Redaction) {
            switch ($phase) {
                case Phase::NonRemplie:
                    QuestionController::error("afficherAccueil", "La question n'est pas encore prête. Vous ne pouvez pas encore écrire de proposition.");
                    break;
                case Phase::Attente:
                    QuestionController::error("afficherAccueil", "La question n'est pas encore prête. Vous ne pouvez pas encore écrire de proposition.");
                    break;
                case Phase::Vote:
                    QuestionController::error("afficherAccueil", "La question est en cours de vote. Vous ne pouvez plus écrire de proposition.");
                    break;
                case Phase::Resultat:
                    QuestionController::error("afficherAccueil", "La question est terminée. Vous ne pouvez plus écrire de proposition.");
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
            static::error("afficherAccueil", "Veuillez sélectionner la question pour laquelle vous souhaitez écrire une proposition.");
            return;
        }

        $question = Question::toQuestion((new QuestionRepository())->select($_POST['idQuestion']));

        if (!$question) {
            static::error("afficherAccueil", "La question n'existe pas");
            return;
        } else if (!isset($_POST['titreProposition'])) {
            static::error("afficherAccueil", "Veuillez saisir un titre pour votre proposition.");
            return;
        }
        $titreProposition = $_POST['titreProposition'];
        $idResponsable = $_POST['idResponsable'];

        $responsable = (new UtilisateurRepository())->select($idResponsable);

        if (!$responsable) {
            static::error("afficherAccueil", "Le responsable n'existe pas");
            return;
        }

        /*
        TODO : vérifier si l'utilisateur n'a pas déjà écrit une proposition pour cette question
        */

        $phase = $question->getPhase();
        switch ($phase) {
            case Phase::NonRemplie:
                QuestionController::error("afficherAccueil", "La question n'est pas encore prête. Vous ne pouvez pas encore écrire de proposition.");
                break;
            case Phase::Attente:
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
            static::error("afficherAccueil", "Veuillez saisir un titre pour votre proposition.");
            return;
        } else if (strlen($titreProposition) > 100) {
            static::error("afficherAccueil", "Le titre de votre proposition ne doit pas dépasser 100 caractères.");
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
                static::error("afficherAccueil", "Veuillez saisir un contenu pour votre proposition.");
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
            static::error("afficherAccueil", "Vous ne faites pas partie des rédacteurs de cette question.");
            return;
        }

        $propositionExiste = (new PropositionRepository())->selectByQuestionEtRedacteur($question->getIdQuestion(), $idResponsable) == null ? false : true;
        if ($propositionExiste) {
            static::error("afficherAccueil", "Vous avez déjà écrit une proposition pour cette question.");
            return;
        }

        (new PropositionRepository())->insert($proposition);

        static::message("afficherAccueil", "La proposition a bien été enregistrée.");
    }

    public static function afficherFormulaireContribuerProposition()
    {
        if (!isset($_GET['idProposition']) || !is_numeric($_GET['idProposition'])) {
            static::error("afficherAccueil", "Aucune proposition n'a été sélectionnée");
            return;
        }

        $idProposition = $_GET['idProposition'];

        $proposition = (new PropositionRepository())->select($idProposition);
        if (!$proposition) {
            static::error("afficherAccueil", "La proposition n'existe pas");
            return;
        }
        $proposition = Proposition::toProposition($proposition);

        $phase = $proposition->getQuestion()->getPhase();
        if ($phase !== Phase::Redaction) {
            switch ($phase) {
                case Phase::NonRemplie:
                    QuestionController::error("afficherAccueil", "La question n'est pas encore prête. Vous ne pouvez pas encore contribuer à une proposition.");
                    break;
                case Phase::Attente:
                    QuestionController::error("afficherAccueil", "La question n'est pas encore prête. Vous ne pouvez pas encore contribuer à une proposition.");
                    break;
                case Phase::Vote:
                    QuestionController::error("afficherAccueil", "La question est en cours de vote. Vous ne pouvez plus contribuer à une proposition.");
                    break;
                case Phase::Resultat:
                    QuestionController::error("afficherAccueil", "La question est terminée. Vous ne pouvez plus contribuer à une proposition.");
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
            static::error("afficherAccueil", "Veuillez sélectionner la proposition pour laquelle vous souhaitez contribuer");
            return;
        }

        $proposition = Proposition::toProposition((new PropositionRepository())->select($_POST['idProposition']));

        if (!$proposition) {
            static::error("afficherAccueil", "La proposition n'existe pas");
            return;
        } else if (!isset($_POST['titreProposition'])) {
            static::error("afficherAccueil", "Veuillez saisir un titre pour votre proposition.");
        }


        $phase = $proposition->getQuestion()->getPhase();
        switch ($phase) {
            case Phase::NonRemplie:
                QuestionController::error("afficherAccueil", "La question n'est pas encore prête. Vous ne pouvez pas encore contribuer pour la proposition.");
                break;
            case Phase::Attente:
                QuestionController::error("afficherAccueil", "La question n'est pas encore prête. Vous ne pouvez pas encore contribuer pour la proposition.");
                break;
            case Phase::Vote:
                QuestionController::error("afficherAccueil", "La question est en cours de vote. Vous ne pouvez plus contribuer pour la proposition.");
                break;
            case Phase::Resultat:
                QuestionController::error("afficherAccueil", "La question est terminée. Vous ne pouvez plus contribuer pour la proposition.");
                break;
        }



        $paragraphes = [];
        $sections = $proposition->getQuestion()->getSections();
        $estRedacteur = (new QuestionRepository())->estRedacteur($proposition->getQuestion()->getIdQuestion(), $_POST['idCoAuteur']);
        $estCoAuteur = $estRedacteur; // si l'utilisateur est rédacteur, il a les droits d'édition

        for ($i = 0; $i < count($sections); $i++) {

            $nom_paragraphe = 'section_' . $i;
            if (!isset($_POST[$nom_paragraphe]) || $_POST[$nom_paragraphe] == "") {
                static::error("afficherAccueil", "Veuillez saisir un contenu pour votre proposition.");
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
            static::error("afficherAccueil", "Vous ne faites pas partie des co-auteurs ou des rédacteurs de cette proposition.");
            return;
        }

        if($estRedacteur){
            $proposition->setTitreProposition($_POST['titreProposition']);
            (new PropositionRepository())->update($proposition);
        }

        foreach ($paragraphes as $paragraphe) {
            (new ParagrapheRepository())->update($paragraphe);
        }
        static::message("afficherAccueil", "La proposition a bien été enregistrée.");
    }

    public
    static function afficherFormulaireGererCoAuteurs()
    {
        if (!isset($_GET['idProposition']) || !is_numeric($_GET['idProposition'])) {
            static::error("afficherAccueil", "Aucune proposition n'a été sélectionnée.");
            return;
        }
        $idProposition = intval($_GET['idProposition']);

        $proposition = Proposition::toProposition((new PropositionRepository())->select($idProposition));
        if (!$proposition) {
            static::error("afficherAccueil", "La proposition n'existe pas.");
            return;
        }

        $question = $proposition->getQuestion();
        $phase = $question->getPhase();
        switch ($phase) {
            case Phase::NonRemplie:
                QuestionController::error("afficherAccueil", "La question n'est pas encore prête. Vous ne pouvez pas encore gérer les co-auteurs.");
                break;
            case Phase::Attente:
                QuestionController::error("afficherAccueil", "La question n'est pas encore prête. Vous ne pouvez pas encore gérer les co-auteurs.");
                break;
            case Phase::Vote:
                QuestionController::error("afficherAccueil", "La question est en cours de vote. Vous ne pouvez plus gérer les co-auteurs.");
                break;
            case Phase::Resultat:
                QuestionController::error("afficherAccueil", "La question est terminée. Vous ne pouvez plus gérer les co-auteurs.");
                break;
        }

        $utilisateurs = (new UtilisateurRepository())->selectAll();

        $utilisateursAutorises = array_values(array_filter($utilisateurs, function ($utilisateur) use ($proposition) {
            return $utilisateur->getIdUtilisateur() != $proposition->getRedacteur()->getIdUtilisateur();
        }));

        static::afficherVue('view.php', [
            "titrePage" => "Gérer les co-auteurs",
            "contenuPage" => "formulaireGererCoAuteurs.php",
            "proposition" => $proposition,
            "utilisateursAutorises" => $utilisateursAutorises,
            "coAuteurs" => $proposition->getCoAuteurs()
        ]);
    }

    public
    static function gererCoAuteurs()
    {
        if (!isset($_POST['idProposition']) || !is_numeric($_POST['idProposition'])) {
            static::error("afficherAccueil", "Aucune proposition n'a été sélectionnée.");
            return;
        }
        $idProposition = intval($_POST['idProposition']);

        $proposition = Proposition::toProposition((new PropositionRepository())->select($idProposition));
        if (!$proposition) {
            DemandeQuestionController::error("afficherAccueil", "La proposition n'existe pas.");
            return;
        }

        $question = $proposition->getQuestion();
        $phase = $question->getPhase();
        switch ($phase) {
            case Phase::NonRemplie:
                QuestionController::error("afficherAccueil", "La question n'est pas encore prête. Vous ne pouvez pas encore gérer les co-auteurs.");
                break;
            case Phase::Attente:
                QuestionController::error("afficherAccueil", "La question n'est pas encore prête. Vous ne pouvez pas encore gérer les co-auteurs.");
                break;
            case Phase::Vote:
                QuestionController::error("afficherAccueil", "La question est en cours de vote. Vous ne pouvez plus gérer les co-auteurs.");
                break;
            case Phase::Resultat:
                QuestionController::error("afficherAccueil", "La question est terminée. Vous ne pouvez plus gérer les co-auteurs.");
                break;
        }

        $coAuteurs = [];
        foreach ($_POST as $key => $value) {
            if (substr($key, 0, 9) == "co_auteur") {
                $idCoAuteur = intval($value);
                $coAuteur = Utilisateur::toUtilisateur((new UtilisateurRepository())->select($idCoAuteur));
                if ($coAuteur && !in_array($coAuteur, $coAuteurs)) {
                    $coAuteurs[] = $coAuteur;
                }
            }
        }

        if (in_array($proposition->getRedacteur(), $coAuteurs)) {
            $_GET['idProposition'] = $idProposition;
            static::error("afficherFormulaireGererCoAuteurs", "Vous ne pouvez pas être co-auteur de votre propre proposition.");
        }

        (new PropositionRepository)->deleteCoAuteurs($proposition->getIdProposition());
        foreach ($coAuteurs as $coAuteur) {
            (new PropositionRepository)->addCoAuteurGlobal($proposition->getIdProposition(), $coAuteur->getIdUtilisateur());
        }

        static::message("afficherAccueil", "Les co-auteurs ont bien été modifiés.");
    }

    public static function afficherPropositions(){
        if (!isset($_GET['idQuestion']) || !is_numeric($_GET['idQuestion'])) {
            static::error("afficherAccueil", "Aucune question n'a été sélectionnée");
            return;
        }

        $idQuestion = $_GET['idQuestion'];

        $question = (new QuestionRepository())->select($idQuestion);
        if (!$question) {
            static::error("afficherAccueil", "La question n'existe pas");
            return;
        }

        if (!isset($_GET['index'])) {
            $index = 0;
        }
        else {
            $index = $_GET['index'];
        }

        $question = Question::toQuestion($question);

        $propositions = (new PropositionRepository())->selectAllByQuestion($idQuestion);

        static::afficherVue("view.php", [
            "titrePage" => "Propositions",
            "contenuPage" => "afficherPropositions.php",
            "idQuestion" => $idQuestion,
            "question" => $question,
            "propositions" => $propositions,
            "index" => $index
        ]);
    }
}
