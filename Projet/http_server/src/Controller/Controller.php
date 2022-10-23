<?php

namespace App\SAE\Controller;

use App\SAE\Model\Repository\DemandeQuestionRepository as DemandeQuestionRepository;
use App\SAE\Model\DataObject\DemandeQuestion as DemandeQuestion;
use App\SAE\Model\Repository\UtilisateurRepository as UtilisateurRepository;
use App\SAE\Model\DataObject\Utilisateur as Utilisateur;

class Controller
{

    private static function afficherVue(string $cheminVue, array $parametres = []): void
    {
        extract($parametres); // Crée des variables à partir du tableau $parametres
        require __DIR__ . '/../view/' . $cheminVue;
    }

    /**
     * Affiche un message avant de rediriger vers la liste des demandes.
     */
    private static function message(string $titrePage, string $message): void
    {
        static::afficherVue("view.php", [
            "titrePage" => $titrePage,
            "contenuPage" => "message.php",
            "message" => $message,
            "demandes" => (new DemandeQuestionRepository)->selectAll()
        ]);
    }

    public static function listerDemandesQuestion(): void
    {
        $demandes = (new DemandeQuestionRepository)->selectAll();

        static::afficherVue("view.php", [
            "titrePage" => "Liste des demandes",
            "contenuPage" => "listeDemandesQuestion.php",
            "demandes" => $demandes
        ]);
    }

    public static function refuserQuestion(): void
    {
        // TODO: à modifier
        $idQuestion = intval($_GET['idQuestion']);
        (new DemandeQuestionRepository)->delete($idQuestion);
        static::listerDemandesQuestion();
    }

    public static function accepterQuestion(): void
    {
        // TODO: à modifier
        $idQuestion = intval($_GET['idQuestion']);

        $question = (new DemandeQuestionRepository)->select($idQuestion);
        $question->setEstValide(true);

        (new DemandeQuestionRepository)->update($question);
        static::listerDemandesQuestion();
    }

    public static function afficherFormulaireDemandeQuestion(): void
    {
        static::afficherVue("view.php", [
            "titrePage" => "Demande de question",
            "contenuPage" => "demandeQuestion.php"
        ]);
    }

    public static function demanderCreationQuestion(): void
    {
        $titre = $_POST['titre'];
        $intitule = $_POST['intitule'];
        $idUtilisateur = intval($_POST['idUtilisateur']);

        $demande = new DemandeQuestion(-1, $titre, $intitule, false, (new UtilisateurRepository)->select($idUtilisateur));

        (new DemandeQuestionRepository)->insert($demande);

        static::message("Demande effectuée", "Votre demande de question a bien été prise en compte. Elle sera publiée après validation par un administrateur.");
    }

    

    
}
