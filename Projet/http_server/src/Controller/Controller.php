<?php

namespace App\SAE\Controller;

use App\SAE\Model\Repository\DemandeQuestionRepository as DemandeQuestionRepository;
use App\SAE\Model\DataObject\DemandeQuestion as DemandeQuestion;
use App\SAE\Model\Repository\QuestionRepository as QuestionRepository;
use App\SAE\Model\DataObject\Question as Question;
use App\SAE\Model\DataObject\Section;
use App\SAE\Model\Repository\UtilisateurRepository as UtilisateurRepository;
use App\SAE\Model\DataObject\Utilisateur as Utilisateur;
use DateTime;
use DateInterval;

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

    public static function refuserDemandeQuestion(): void
    {
        $demandes = (new DemandeQuestionRepository)->selectAll();

        $idQuestion = intval($_GET['idQuestion']);
        (new DemandeQuestionRepository)->delete($idQuestion);

        self::afficherVue("view.php", [
            "titrePage" => "Liste des demandes",
            "contenuPage" => "listeDemandesQuestion.php",
            "message" => "La demande a été refusée",
            "demandes" => $demandes]);
    }

    public static function accepterDemandeQuestion(): void
    {
        $idQuestion = intval($_GET['idQuestion']);
        $demande = (new DemandeQuestionRepository)->select($idQuestion);
        $question = new Question(
            -1,
            $demande->getTitre(),
            $demande->getIntitule(),
            $demande->getOrganisateur(),
            null,
            null,
            null,
            null,
            null
        );
        (new QuestionRepository)->insertEbauche($question);

        $demandes = (new DemandeQuestionRepository)->selectAll();

        self::afficherVue("view.php", [
            "titrePage" => "Liste des demandes",
            "contenuPage" => "listeDemandesQuestion.php",
            "message" => "La demande a été acceptée",
            "demandes" => $demandes]);
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
        $intitule = $_POST['intitule'];
        $idUtilisateur = intval($_POST['idUtilisateur']);

        $demande = new DemandeQuestion(-1, $titre, $intitule, (new UtilisateurRepository)->select($idUtilisateur));

        (new DemandeQuestionRepository)->insert($demande);

        static::message("Demande effectuée", "Votre demande de question a bien été prise en compte. Elle sera publiée après validation par un administrateur.");
    }

    public static function afficherFormulairePoserQuestion(): void
    {   
        $idQuestion = intval($_GET['idQuestion']);
        $question = (new QuestionRepository)->select($idQuestion);

        static::afficherVue("view.php", [
            "titrePage" => "Poser une question",
            "contenuPage" => "formulairePoserQuestion.php",
            "question" => $question
        ]);
    }

    public static function poserQuestion(): void
    {
        echo '<pre>'; print_r($_POST); echo '</pre>';

        $idQuestion = intval($_POST['idQuestion']);
        $titre = $_POST['titre'];
        $intitule = $_POST['intitule'];
        $idUtilisateur = intval($_POST['idUtilisateur']);
        $nbSections = intval($_POST['nbSections']);
        $sections = [];
        for ($i = 0; $i < $nbSections; $i++) {
            $sections[] = new Section(-1, -1, $_POST['section_' . $i]);
        }

        $dateDebutRedaction = new DateTime($_POST['dateDebutRedaction']);
        $heureDebutRedaction = preg_split('/\D/', $_POST['heureDebutRedaction']);
        $dateDebutRedaction->setTime($heureDebutRedaction[0], $heureDebutRedaction[1]);

        $dateFinRedaction = new DateTime($_POST['dateFinRedaction']);
        $heureFinRedaction = preg_split('/\D/', $_POST['heureFinRedaction']);
        $dateFinRedaction->setTime($heureFinRedaction[0], $heureFinRedaction[1]);

        $dateOuvertureVotes = new DateTime($_POST['dateOuvertureVotes']);
        $heureOuvertureVotes = preg_split('/\D/', $_POST['heureOuvertureVotes']);
        $dateOuvertureVotes->setTime($heureOuvertureVotes[0], $heureOuvertureVotes[1]);

        $dateFermetureVotes = new DateTime($_POST['dateFermetureVotes']);
        $heureFermetureVotes = preg_split('/\D/', $_POST['heureFermetureVotes']);
        $dateFermetureVotes->setTime($heureFermetureVotes[0], $heureFermetureVotes[1]);

        $question = new Question(
            $idQuestion,
            $titre,
            $intitule,
            (new UtilisateurRepository)->select($idUtilisateur),
            $sections,
            $dateDebutRedaction,
            $dateFinRedaction,
            $dateOuvertureVotes,
            $dateFermetureVotes
        );

        (new QuestionRepository)->updateEbauche($question);

    }

    public static function listerMesQuestions() {
        $idUtilisateur = intval($_GET['idUtilisateur']);
        $questions = (new QuestionRepository)->getQuestionsParOrganisateur($idUtilisateur);

        static::afficherVue("view.php", [
            "titrePage" => "Mes questions",
            "contenuPage" => "listeMesQuestions.php",
            "questions" => $questions
        ]);
        
    }
}
