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

    private static ?string $message = NULL;
    private static ?string $messageType = "message";

    private static function afficherVue(string $cheminVue, array $parametres = []): void
    {
        $parametres['message'] = self::$message;
        $parametres['messageType'] = self::$messageType;
        extract($parametres); // Crée des variables à partir du tableau $parametres
        require __DIR__ . '/../view/' . $cheminVue;
    }

    /**
     * Affiche le message $message sur la page associée à $action
     */
    private static function message(string $action, string $message): void
    {
        self::$message = $message;
        self::$action();
    }

    private static function error(string $action, string $message): void
    {
        self::$messageType = "errorMessage";
        self::message($action, $message);
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

        self::message("listerDemandesQuestion", "La question a été refusée");
    }

    public static function accepterDemandeQuestion(): void
    {
        $idQuestion = intval($_GET['idQuestion']);
        $demande = (new DemandeQuestionRepository)->select($idQuestion);
        $question = new Question(
            -1,
            $demande->getTitre(),
            $demande->getDescription(),
            $demande->getOrganisateur(),
            null,
            null,
            null,
            null,
            null
        );
        (new QuestionRepository)->insertEbauche($question);
        (new DemandeQuestionRepository)->delete($idQuestion);

        self::message("listerDemandesQuestion", "La question a été acceptée");
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

        if($titre == "" || $description == "") {
            self::error("afficherFormulaireDemandeQuestion", "Veuillez remplir tous les champs");
            return;
        }

        if(strlen($titre) > 100) {
            self::error("afficherFormulaireDemandeQuestion", "Le titre ne doit pas dépasser 100 caractères");
            return;
        }

        $demande = new DemandeQuestion(-1, $titre, $description, (new UtilisateurRepository)->select($idUtilisateur));

        (new DemandeQuestionRepository)->insert($demande);

        static::message("listerDemandesQuestion", "Votre demande a été envoyée");
    }

    public static function afficherFormulairePoserQuestion(): void
    {   
        $idQuestion = intval($_GET['idQuestion']);
        $question = Question::toQuestion((new QuestionRepository)->select($idQuestion));


        static::afficherVue("view.php", [
            "titrePage" => "Poser une question",
            "contenuPage" => "formulairePoserQuestion.php",
            "question" => $question
        ]);
    }

    public static function poserQuestion(): void
    {
        $idQuestion = intval($_POST['idQuestion']);
        $titre = $_POST['titre'];
        $intitule = $_POST['intitule'];
        $idUtilisateur = intval($_POST['idUtilisateur']);
        $nbSections = intval($_POST['nbSections']);

        if($nbSections == 0){
            $_GET['idUtilisateur'] = $idUtilisateur;
            static::error("listerMesQuestions", "Vous devez ajouter au moins une section");
            return;
        }

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

        $dateCoherentes = $dateDebutRedaction < $dateFinRedaction && $dateFinRedaction <= $dateOuvertureVotes && $dateOuvertureVotes < $dateFermetureVotes;

        if(!$dateCoherentes) {
            $_GET['idUtilisateur'] = $idUtilisateur;
            static::error("listerMesQuestions", "Les dates ne sont pas cohérentes");
            return;
        }

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

        $_GET['idUtilisateur'] = $idUtilisateur;
        static::message("listerMesQuestions", "La question a été posée");
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
