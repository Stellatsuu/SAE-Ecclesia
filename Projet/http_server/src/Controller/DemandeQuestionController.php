<?php

namespace App\SAE\Controller;

use App\SAE\Model\Repository\QuestionRepository as QuestionRepository;
use App\SAE\Model\DataObject\Question as Question;
use App\SAE\Model\Repository\DemandeQuestionRepository as DemandeQuestionRepository;
use App\SAE\Model\DataObject\DemandeQuestion as DemandeQuestion;
use App\SAE\Model\Repository\UtilisateurRepository as UtilisateurRepository;

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
        $demandes = (new DemandeQuestionRepository)->selectAll();

        $idQuestion = intval($_GET['idQuestion']);
        (new DemandeQuestionRepository)->delete($idQuestion);

        static::message("listerDemandesQuestion", "La question a été refusée");
    }

    public static function accepterDemandeQuestion(): void
    {
        $idQuestion = intval($_GET['idQuestion']);
        $demande = DemandeQuestion::toDemandeQuestion((new DemandeQuestionRepository)->select($idQuestion));
        $question = new Question(
            -1,
            $demande->getTitre(),
            $demande->getDescription(),
            $demande->getOrganisateur(),
            null,
            null,
            null,
            null,
            null,
            null,
            null
        );
        (new QuestionRepository)->insertEbauche($question);
        (new DemandeQuestionRepository)->delete($idQuestion);

        static::message("listerDemandesQuestion", "La question a été acceptée");
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
        $titre = $_POST['titre'];
        $description = $_POST['description'];
        $idUtilisateur = intval($_POST['idUtilisateur']);

        // Vérification des données
        if ($titre == "" || $description == "") {
            static::error("afficherFormulaireDemandeQuestion", "Veuillez remplir tous les champs");
            return;
        }

        if (strlen($titre) > 100) {
            static::error("afficherFormulaireDemandeQuestion", "Le titre ne doit pas dépasser 100 caractères");
            return;
        }

        if (strlen($description) > 4000) {
            static::error("afficherFormulaireDemandeQuestion", "La description ne doit pas dépasser 4000 caractères");
            return;
        }

        $demande = new DemandeQuestion(-1, $titre, $description, (new UtilisateurRepository)->select($idUtilisateur));

        (new DemandeQuestionRepository)->insert($demande);

        static::message("listerDemandesQuestion", "Votre demande a été envoyée");
    }

}
