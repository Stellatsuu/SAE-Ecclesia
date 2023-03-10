<?php

namespace App\SAE\Controller;

use App\SAE\Lib\ConnexionUtilisateur;
use App\SAE\Model\DataObject\Paragraphe;
use App\SAE\Model\DataObject\Proposition;
use App\SAE\Model\DataObject\Question;
use App\SAE\Model\DataObject\Utilisateur;
use App\SAE\Model\Repository\ParagrapheRepository;
use App\SAE\Model\Repository\PropositionRepository;
use App\SAE\Model\Repository\QuestionRepository;
use App\SAE\Model\Repository\UtilisateurRepository;
use App\SAE\Lib\PhaseQuestion as Phase;
use App\SAE\Model\Repository\CoAuteurRepository;
use App\SAE\Model\Repository\RedacteurRepository;
use App\SAE\Model\Repository\VotantRepository;
use App\SAE\Model\Repository\VoteRepository;

class PropositionController extends MainController
{

    public static function afficherFormulaireEcrireProposition()
    {
        $username = ConnexionUtilisateur::getUsernameSiConnecte();

        $idQuestion = static::getIfSetAndNumeric("idQuestion");

        $estRedacteur = (new RedacteurRepository())->existsForQuestion($idQuestion, $username);
        if (!$estRedacteur) {
            static::error(Q_URL . $idQuestion, "Vous ne faites pas partie des rédacteurs de cette question.");
        }

        $question = Question::castIfNotNull((new QuestionRepository)->select($idQuestion));
        $phase = $question->getPhase();
        if ($phase !== Phase::Redaction) {
            switch ($phase) {
                case Phase::Attente:
                case Phase::NonRemplie:
                    QuestionController::error(LQ_URL, "La question n'est pas encore prête. Vous ne pouvez pas encore écrire de proposition.");
                    break;
                case Phase::Vote:
                    QuestionController::error(LQ_URL, "La question est en cours de vote. Vous ne pouvez plus écrire de proposition.");
                    break;
                case Phase::Resultat:
                    QuestionController::error(LQ_URL, "La question est terminée. Vous ne pouvez plus écrire de proposition.");
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
        $username = ConnexionUtilisateur::getUsernameSiConnecte();

        $idQuestion = static::getIfSetAndNumeric("idQuestion");
        $question = Question::castIfNotNull((new QuestionRepository())->select($idQuestion));

        $responsable = Utilisateur::castIfNotNull((new UtilisateurRepository())->select($username));

        $titreProposition = static::getIfSetAndNotEmpty("titreProposition");

        $phase = $question->getPhase();
        switch ($phase) {
            case Phase::Attente:
            case Phase::NonRemplie:
                QuestionController::error(LQ_URL, "La question n'est pas encore prête. Vous ne pouvez pas encore écrire de proposition.");
                break;
            case Phase::Vote:
                QuestionController::error(LQ_URL, "La question est en cours de vote. Vous ne pouvez plus écrire de proposition.");
                break;
            case Phase::Resultat:
                QuestionController::error(LQ_URL, "La question est terminée. Vous ne pouvez plus écrire de proposition.");
                break;
        }

        if (strlen($titreProposition) > 100) {
            static::error(Q_URL . $idQuestion, "Le titre de votre proposition ne doit pas dépasser 100 caractères.");
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
            $contenu = static::getIfSetAndNotEmpty($nom_paragraphe, Q_URL . $idQuestion, "Le contenu de la section " . ($i + 1) . " est vide.");

            $paragraphe = new Paragraphe(
                -1,
                $proposition->getIdProposition(),
                $sections[$i],
                $contenu
            );
            $paragraphes[] = $paragraphe;
        }

        $proposition->setParagraphes($paragraphes);

        $estRedacteur = (new RedacteurRepository())->existsForQuestion($question->getIdQuestion(), $username);
        if (!$estRedacteur) {
            static::error(Q_URL . $idQuestion, "Vous ne faites pas partie des rédacteurs de cette question.");
        }

        $propositionExiste = (new PropositionRepository())->selectByQuestionEtResponsable($question->getIdQuestion(), $username) == null ? false : true;
        if ($propositionExiste) {
            static::error(Q_URL . $idQuestion, "Vous avez déjà écrit une proposition pour cette question.");
        }

        (new PropositionRepository())->insert($proposition);

        static::message(Q_URL . $idQuestion, "La proposition a bien été enregistrée.");
    }

    public static function afficherFormulaireContribuerProposition()
    {
        $username = ConnexionUtilisateur::getUsernameSiConnecte();

        $idProposition = static::getIfSetAndNumeric("idProposition");
        $proposition = Proposition::castIfNotNull((new PropositionRepository())->select($idProposition));
        $idQuestion = $proposition->getIdQuestion();

        $estResponsable = $proposition->getUsernameResponsable() == $username;
        $estCoAuteur = (new CoAuteurRepository())->existsForProposition($idProposition, $username);

        if (!$estResponsable && !$estCoAuteur) {
            static::error(Q_URL . $idQuestion, "Vous n'êtes pas autorisé à contribuer à cette proposition.");
        }

        $phase = $proposition->getQuestion()->getPhase();
        if ($phase !== Phase::Redaction) {
            switch ($phase) {
                case Phase::Attente:
                case Phase::NonRemplie:
                    QuestionController::error(LQ_URL, "La question n'est pas encore prête. Vous ne pouvez pas encore contribuer à une proposition.");
                    break;
                case Phase::Vote:
                    QuestionController::error(LQ_URL, "La question est en cours de vote. Vous ne pouvez plus contribuer à une proposition.");
                    break;
                case Phase::Resultat:
                    QuestionController::error(LQ_URL, "La question est terminée. Vous ne pouvez plus contribuer à une proposition.");
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
        $username = ConnexionUtilisateur::getUsernameSiConnecte();

        $idProposition = static::getIfSetAndNumeric("idProposition");
        $proposition = Proposition::castIfNotNull((new PropositionRepository())->select($idProposition));
        $idQuestion = $proposition->getIdQuestion();

        $phase = $proposition->getQuestion()->getPhase();
        switch ($phase) {
            case Phase::Attente:
            case Phase::NonRemplie:
                QuestionController::error(LQ_URL, "La question n'est pas encore prête. Vous ne pouvez pas encore contribuer pour la proposition.");
                break;
            case Phase::Vote:
                QuestionController::error(LQ_URL, "La question est en cours de vote. Vous ne pouvez plus contribuer pour la proposition.");
                break;
            case Phase::Resultat:
                QuestionController::error(LQ_URL, "La question est terminée. Vous ne pouvez plus contribuer pour la proposition.");
                break;
        }

        $paragraphes = [];
        $sections = $proposition->getQuestion()->getSections();
        $estResponsable = $proposition->getUsernameResponsable() == $username;
        $estCoAuteur = (new CoAuteurRepository())->existsForProposition($idProposition, $username);

        for ($i = 0; $i < count($sections); $i++) {

            $nom_paragraphe = 'section_' . $i;
                $contenu = static::getIfSetAndNotEmpty($nom_paragraphe, Q_URL . $idQuestion, "Le contenu de la section " . ($i + 1) . " est vide.");

            $paragraphe = (new ParagrapheRepository())->selectByPropositionEtSection($proposition->getIdProposition(), $sections[$i]->getIdSection());
            $paragraphe->setContenuParagraphe($contenu);
            $paragraphes[] = $paragraphe;
        }

        if (!$estCoAuteur && !$estResponsable) {
            static::error(Q_URL . $idQuestion, "Vous n'êtes pas un des co-auteurs ou le responsable de cette proposition.");
        }

        if ($estResponsable) {
            $proposition->setTitreProposition($_POST['titreProposition']);
            (new PropositionRepository())->update($proposition);
        }

        foreach ($paragraphes as $paragraphe) {
            (new ParagrapheRepository())->update($paragraphe);
        }
        static::message(Q_URL . $idQuestion, "La proposition a bien été enregistrée.");
    }

    public static function afficherPropositions()
    {
        $username = ConnexionUtilisateur::estConnecte() ? ConnexionUtilisateur::getUsername() : "";

        $idQuestion = static::getIfSetAndNumeric("idQuestion");
        $question = Question::castIfNotNull((new QuestionRepository())->select($idQuestion));
        $systemeVote = $question->getSystemeVote();
        $phase = $question->getPhase();

        $propositions = (new PropositionRepository())->selectAllByQuestion($idQuestion);
        $idPropositions = array_map(function ($proposition) {
            return $proposition->getIdProposition();
        }, $propositions);

        //permet de passer soit un index, soit un id de proposition dans l'URL
        if(isset($_GET['idProposition'])) {
            $idProposition = static::getIfSetAndNumeric("idProposition");
            $index = array_search($idProposition, $idPropositions);
            if($index === false) {
                static::error("frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestion", "La proposition n'existe pas ou n'est pas associée à cette question.");
            }
        } else {
            $index = isset($_GET['index']) ? static::getIfSetAndNumeric("index") : 0;
        }

        $nbPropositions = count($propositions);

        if($nbPropositions != 0) {
            $index = $index % $nbPropositions;
            $propositionAffichee = $propositions[$index];
        } else {
            $propositionAffichee = null;
        }

        $estOrganisateur = $propositionAffichee != null && $question->getUsernameOrganisateur() == $username;
        $estResponsable = $propositionAffichee != null && $propositionAffichee->getUsernameResponsable() == $username;
        $estCoAuteur = $propositionAffichee != null && (new CoAuteurRepository())->existsForProposition($propositionAffichee->getIdProposition(), $username);
        $estVotant = $propositionAffichee != null && (new VotantRepository())->existsForQuestion($idQuestion, $username);

        $peutSupprimerOrga = $estOrganisateur && ($phase == Phase::Redaction || $phase == Phase::Lecture);
        $peutSupprimerResp = $estResponsable && ($phase == Phase::Redaction);

        $peutSupprimer = $peutSupprimerOrga || $peutSupprimerResp;
        $peutEditer = $phase == Phase::Redaction && ($estResponsable || $estCoAuteur);
        $peutVoter = $phase == Phase::Vote && $estVotant;
        $peutGererCoAuteurs = $estResponsable && $phase == Phase::Redaction;
        $peutDemanderCoAuteur = !$estCoAuteur && !$estResponsable && $phase == Phase::Redaction;

        $aDejaVote = (new VoteRepository)->existsForQuestion($idQuestion, $username);

        $dataQuestion = [
            "idQuestion" => $question->getIdQuestion(),
            "titreQuestion" => $question->getTitre(),
            "descriptionQuestion" => $question->getDescription(),
            "nomUsuelOrganisateur" => $question->getOrganisateur()->getNomUsuel(),
            "interfaceVote" => $systemeVote->afficherInterfaceVote()
        ];

        $dataProposition = $propositionAffichee == null ? null :
        [
            "idProposition" => $propositionAffichee->getIdProposition(),
            "titreProposition" => $propositionAffichee->getTitreProposition(),
            "nomUsuelResponsable" => $propositionAffichee->getResponsable()->getNomUsuel(),
            
            "paragraphes" => array_map(function ($paragraphe) {
                return [
                    "titre" => $paragraphe->getSection()->getNomSection(),
                    "contenu" => $paragraphe->getContenuParagraphe(),
                ];
            }, $propositionAffichee->getParagraphes()),
        ];

        static::afficherVue("view.php", [
            "titrePage" => "Propositions",
            "contenuPage" => "afficherPropositions.php",
            "dataQuestion" => $dataQuestion,
            "dataProposition" => $dataProposition,
            "index" => $index,
            "nbPropositions" => $nbPropositions,
            "peutSupprimer" => $peutSupprimer,
            "peutEditer" => $peutEditer,
            "peutVoter" => $peutVoter,
            "peutGererCoAuteurs" => $peutGererCoAuteurs,
            "peutDemanderCoAuteur" => $peutDemanderCoAuteur,
            "aDejaVote" => $aDejaVote
        ]);
    }

    public static function supprimerProposition()
    {
        $username = ConnexionUtilisateur::getUsernameSiConnecte();

        $idProposition = static::getIfSetAndNumeric("idProposition");

        $proposition = Proposition::castIfNotNull((new PropositionRepository())->select($idProposition));

        $question = $proposition->getQuestion();
        $phase = $question->getPhase();
        switch ($phase) {
            case Phase::Attente:
            case Phase::NonRemplie:
                QuestionController::error(LQ_URL, "La question n'est pas encore prête. Vous ne pouvez pas encore supprimer de proposition.");
                break;
            case Phase::Vote:
                QuestionController::error(LQ_URL, "La question est en cours de vote. Vous ne pouvez plus supprimer de proposition.");
                break;
            case Phase::Resultat:
                QuestionController::error(LQ_URL, "La question est terminée. Vous ne pouvez plus supprimer de proposition.");
                break;
        }

        $estResponsable = $proposition->getUsernameResponsable() == $username;
        $estOrganisateur = $question->getUsernameOrganisateur() == $username;

        /**
         * @var string URL de afficherPropositions
         */
        $AP_URL = "frontController.php?controller=proposition&action=afficherPropositions&idQuestion=" . $question->getIdQuestion();

        if (!$estResponsable && !$estOrganisateur) {
            static::error($AP_URL, "Vous n'avez pas les droits pour supprimer cette proposition");
        }

        (new PropositionRepository)->delete($idProposition);
        static::message($AP_URL, "La proposition a bien été supprimée.");
    }
}
