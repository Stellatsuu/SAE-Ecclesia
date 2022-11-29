<?php

namespace App\SAE\Controller;

use App\SAE\Model\Repository\QuestionRepository as QuestionRepository;
use App\SAE\Model\DataObject\Question as Question;
use App\SAE\Model\Repository\DemandeQuestionRepository as DemandeQuestionRepository;
use App\SAE\Model\DataObject\DemandeQuestion as DemandeQuestion;
use App\SAE\Model\Repository\UtilisateurRepository as UtilisateurRepository;

const LDQ_URL = "frontController.php?controller=demandeQuestion&action=listerDemandesQuestion";
const AFDQ_URL = "frontController.php?controller=demandeQuestion&action=afficherFormulaireDemandeQuestion";

class DemandeQuestionController extends MainController
{
    public static function listerDemandesQuestion(): void
    {
        $demandes = (new DemandeQuestionRepository)->selectAll();

        static::afficherVue("view.php", [
            "titrePage" => "Liste des demandes",
            "contenuPage" => "listeDemandesQuestion.php",
            "demandes" => $demandes
        ]);
    }

    public static function refuserDemandeQuestion(): void
    {
        $idQuestion = static::getIfSetAndNumeric("idQuestion", LDQ_URL);

        (new DemandeQuestionRepository)->delete($idQuestion);

        static::message("frontController.php?controller=demandeQuestion&action=listerDemandesQuestion", "La demande a été refusée");
    }

    public static function accepterDemandeQuestion(): void
    {
        $idQuestion = static::getIfSetAndNumeric("idQuestion", LDQ_URL);

        $demande = DemandeQuestion::castIfNotNull((new DemandeQuestionRepository)->select($idQuestion));
        $question = new Question(
            -1,
            $demande->getTitre(),
            $demande->getDescription(),
            $demande->getOrganisateur()
        );

        (new QuestionRepository)->insertEbauche($question);
        (new DemandeQuestionRepository)->delete($idQuestion);

        static::message("frontController.php?controller=demandeQuestion&action=listerDemandesQuestion", "La question a été acceptée");
    }

    public static function afficherFormulaireDemandeQuestion(): void
    {
        static::afficherVue("view.php", [
            "titrePage" => "Demande de question",
            "contenuPage" => "formulaireDemandeQuestion.php"
        ]);
    }

    public static function demanderCreationQuestion(): void
    {
        $titre = static::getIfSetAndNotEmpty("titre", AFDQ_URL);
        $description = static::getIfSetAndNotEmpty("description", AFDQ_URL);
        $idUtilisateur = static::getIfSetAndNumeric("idUtilisateur", AFDQ_URL);

        if (strlen($titre) > 100) {
            static::error(AFDQ_URL, "Le titre ne doit pas dépasser 100 caractères");
        }

        if (strlen($description) > 4000) {
            static::error(AFDQ_URL, "La description ne doit pas dépasser 4000 caractères");
        }

        $demande = new DemandeQuestion(-1, $titre, $description, $idUtilisateur);
        (new DemandeQuestionRepository)->insert($demande);

        static::message("frontController.php?controller=question&action=listerMesQuestions", "Votre demande a été envoyée");
    }
}
