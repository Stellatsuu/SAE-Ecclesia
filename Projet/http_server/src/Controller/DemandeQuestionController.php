<?php

namespace App\SAE\Controller;

use App\SAE\Lib\ConnexionUtilisateur;
use App\SAE\Model\Repository\QuestionRepository as QuestionRepository;
use App\SAE\Model\DataObject\Question as Question;
use App\SAE\Model\Repository\DemandeQuestionRepository as DemandeQuestionRepository;
use App\SAE\Model\DataObject\DemandeQuestion as DemandeQuestion;

class DemandeQuestionController extends MainController
{
    public static function listerDemandesQuestion(): void
    {
        $estAdmin = ConnexionUtilisateur::estAdmin();
        if (!$estAdmin) {
            static::error(ACCUEIL_URL, "Vous devez être administrateur pour accéder à cette page");
        }

        $demandes = (new DemandeQuestionRepository)->selectAll();

        $dataDemandes = array_map(function ($demande) {
            $demande = DemandeQuestion::castIfNotNull($demande);
            return [
                "idQuestion" => $demande->getIdQuestion(),
                "titre" => $demande->getTitre(),
                "description" => $demande->getDescription(),
                "nomUsuelOrganisateur" => $demande->getOrganisateur()->getNomUsuel()
            ];
        }, $demandes);

        static::afficherVue("view.php", [
            "titrePage" => "Liste des demandes",
            "contenuPage" => "listeDemandesQuestion.php",
            "dataDemandes" => $dataDemandes
        ]);
    }

    public static function refuserDemandeQuestion(): void
    {
        $estAdmin = ConnexionUtilisateur::estAdmin();
        if (!$estAdmin) {
            static::error(ACCUEIL_URL, "Vous devez être administrateur pour effectuer cette action");
        }


        $idQuestion = static::getIfSetAndNumeric("idQuestion", LDQ_URL);
        (new DemandeQuestionRepository)->delete($idQuestion);
        static::message(LDQ_URL, "La demande a été refusée");
    }

    public static function accepterDemandeQuestion(): void
    {
        $estAdmin = ConnexionUtilisateur::estAdmin();
        if (!$estAdmin) {
            static::error(ACCUEIL_URL, "Vous devez être administrateur pour effectuer cette action");
        }

        $idQuestion = static::getIfSetAndNumeric("idQuestion", LDQ_URL);

        $demande = DemandeQuestion::castIfNotNull((new DemandeQuestionRepository)->select($idQuestion));

        $question = new Question(
            -1,
            $demande->getTitre(),
            $demande->getDescription(),
            $demande->getUsernameOrganisateur()
        );

        (new QuestionRepository)->insert($question);
        (new DemandeQuestionRepository)->delete($idQuestion);

        static::message(LDQ_URL, "La question a été acceptée");
    }

    public static function afficherFormulaireDemandeQuestion(): void
    {
        ConnexionUtilisateur::getUsernameSiConnecte();

        static::afficherVue("view.php", [
            "titrePage" => "Demande de question",
            "contenuPage" => "formulaireDemandeQuestion.php"
        ]);
    }

    public static function demanderCreationQuestion(): void
    {
        $username = ConnexionUtilisateur::getUsernameSiConnecte();

        $titre = static::getIfSetAndNotEmpty("titre", AFDQ_URL);
        $description = static::getIfSetAndNotEmpty("description", AFDQ_URL);

        if (strlen($titre) > 100) {
            static::error(AFDQ_URL, "Le titre ne doit pas dépasser 100 caractères");
        }

        if (strlen($description) > 4000) {
            static::error(AFDQ_URL, "La description ne doit pas dépasser 4000 caractères");
        }

        $demande = new DemandeQuestion(
            -1,
            $titre,
            $description,
            $username
        );
        (new DemandeQuestionRepository)->insert($demande);
        static::message(LQ_URL, "Votre demande a été envoyée");
    }
}
