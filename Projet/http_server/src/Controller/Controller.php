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

        // Vérification des données
        if ($titre == "" || $description == "") {
            self::error("afficherFormulaireDemandeQuestion", "Veuillez remplir tous les champs");
            return;
        }

        if (strlen($titre) > 100) {
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



        if (
            $question->getDateDebutRedaction() === null
            || $question->getDateFinRedaction() === null
            || $question->getDateOuvertureVotes() === null
            || $question->getDateFermetureVotes() === null
        ) {

            $question->setDateDebutRedaction((new DateTime())->add(new DateInterval('P1D')));
            $question->setDateFinRedaction((new DateTime())->add(new DateInterval('P8D')));
            $question->setDateOuvertureVotes((new DateTime())->add(new DateInterval('P8D')));
            $question->setDateFermetureVotes((new DateTime())->add(new DateInterval('P15D')));
        }

        $datesFormatees = array(
            "dateDebutRedaction" => $question->getDateDebutRedaction()->format("Y-m-d"),
            "dateFinRedaction" => $question->getDateFinRedaction()->format("Y-m-d"),
            "dateOuvertureVotes" => $question->getDateOuvertureVotes()->format("Y-m-d"),
            "dateFermetureVotes" => $question->getDateFermetureVotes()->format("Y-m-d")
        );

        static::afficherVue("view.php", [
            "titrePage" => "Poser une question",
            "contenuPage" => "formulairePoserQuestion.php",
            "question" => $question,
            "datesFormatees" => $datesFormatees
        ]);
    }

    public static function poserQuestion(): void
    {

        $idQuestion = intval($_POST['idQuestion']);
        $titre = $_POST['titre'];
        $intitule = $_POST['intitule'];
        $idUtilisateur = intval($_POST['idUtilisateur']);

        $nbSections = 1;
        $sections = [];

        // Remplissage du _GET pour les messages d'erreur
        $_GET["idQuestion"] = $idQuestion;

        while (isset($_POST['nomSection' . $nbSections]) && isset($_POST['descriptionSection' . $nbSections])) {
            $nomSection = $_POST['nomSection' . $nbSections];
            $descriptionSection = $_POST['descriptionSection' . $nbSections];

            if ($nomSection == "" || $descriptionSection == "") {
                self::error("afficherFormulairePoserQuestion", "Veuillez remplir tous les champs");
                return;
            }

            if (strlen($nomSection) > 50) {
                self::error("afficherFormulairePoserQuestion", "Le nom de la section ne doit pas dépasser 50 caractères");
                return;
            }

            $section = new Section(-1, -1, $nomSection, $descriptionSection);
            $sections[] = $section;
            $nbSections++;
        }

        if (
            !preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $_POST['dateDebutRedaction'])
            || !preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $_POST['dateFinRedaction'])
            || !preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $_POST['dateOuvertureVotes'])
            || !preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $_POST['dateFermetureVotes'])
        ) {
            self::error("afficherFormulairePoserQuestion", "Veuillez entrer des dates valides");
            return;
        }

        if (
            !preg_match("/^[0-9]{2}:[0-9]{2}$/", $_POST['heureDebutRedaction'])
            || !preg_match("/^[0-9]{2}:[0-9]{2}$/", $_POST['heureFinRedaction'])
            || !preg_match("/^[0-9]{2}:[0-9]{2}$/", $_POST['heureOuvertureVotes'])
            || !preg_match("/^[0-9]{2}:[0-9]{2}$/", $_POST['heureFermetureVotes'])
        ) {
            self::error("afficherFormulairePoserQuestion", "Veuillez entrer des heures valides");
            return;
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

        $dateCoherentes =
            $dateDebutRedaction < $dateFinRedaction
            && $dateFinRedaction <= $dateOuvertureVotes
            && $dateOuvertureVotes < $dateFermetureVotes
            && $dateDebutRedaction > (new DateTime("now"));

        // Vérification des données
        if (!$dateCoherentes) {
            $_GET['idUtilisateur'] = $idUtilisateur;
            static::error("listerMesQuestions", "Les dates ne sont pas cohérentes");
            return;
        }

        if ($sections == []) {
            self::error("afficherFormulairePoserQuestion", "Au moins une section est requise");
            return;
        }

        if ($titre == "" || $intitule == "") {
            self::error("afficherFormulairePoserQuestion", "Veuillez remplir tous les champs");
            return;
        }

        if (strlen($titre) > 100) {
            self::error("afficherFormulairePoserQuestion", "Le titre ne doit pas dépasser 100 caractères");
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

    public static function listerMesQuestions()
    {
        $idUtilisateur = intval($_GET['idUtilisateur']);
        $questions = (new QuestionRepository)->getQuestionsParOrganisateur($idUtilisateur);

        static::afficherVue("view.php", [
            "titrePage" => "Mes questions",
            "contenuPage" => "listeMesQuestions.php",
            "questions" => $questions
        ]);
    }
}
