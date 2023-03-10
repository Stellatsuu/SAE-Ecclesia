<?php

namespace App\SAE\Controller;

use App\SAE\Lib\ConnexionUtilisateur;
use App\SAE\Lib\MessageFlash;
use App\SAE\Lib\PhaseQuestion;
use App\SAE\Model\Repository\QuestionRepository;

/**
 * @var string URL de l'accueil
 */
const ACCUEIL_URL = "frontController.php";

/**
 * @var string URL de la liste des demandes de question
 */
const LDQ_URL = "frontController.php?controller=demandeQuestion&action=listerDemandesQuestion";

/**
 * @var string URL du formulaire de demande de question
 */
const AFDQ_URL = "frontController.php?controller=demandeQuestion&action=afficherFormulaireDemandeQuestion";
/**
 * @var string URL de la page "Lister mes questions"
 */
const LMQ_URL = "frontController.php?controller=question&action=listerQuestions&f_mq=true";

/**
 * @var string URL de la page "Lister questions"
 */
const LQ_URL = "frontController.php?controller=question&action=listerQuestions";

/**
 * @var string URL d'une question, id à rajouter
 */
const Q_URL = "frontController.php?controller=question&action=afficherQuestion&idQuestion=";

class MainController
{

    private static bool $isTesting = false;

    public static function setTesting(bool $isTesting): void
    {
        static::$isTesting = $isTesting;
    }

    public static function afficherVue(string $cheminVue, array $parametres): void
    {
        if (static::$isTesting) return;

        extract($parametres); // Crée des variables à partir du tableau $parametres
        require __DIR__ . '/../view/' . $cheminVue;
    }

    public static function message(string $url, string $message): void
    {
        if (!static::$isTesting) {
            MessageFlash::ajouter("info", $message);
            static::redirect($url);
        } else {
            DebugController::logToFile("info: " . $message);
        }
    }

    public static function error(string $url, string $message): void
    {
        if (!static::$isTesting) {
            MessageFlash::ajouter("error", $message);
            static::redirect($url);
        } else {
            DebugController::logToFile("error: " . $message);
            throw new \Exception($message);
        }
    }

    public static function redirect(string $url): void
    {
        if (static::$isTesting) return;

        header("Location: $url");
        exit();
    }

    public static function afficherAccueil(): void
    {
        $questions = (new QuestionRepository())->selectAllListerQuestions(3, 0, [], [], []);
        $username = ConnexionUtilisateur::getUsername() ?: "";

        $donneesQuestions = [];

        foreach ($questions as $question) {
            $phase = $question->getPhase();
            $status = match ($phase) {
                PhaseQuestion::NonRemplie => "Question validée",
                PhaseQuestion::Attente => "En attente",
                PhaseQuestion::Redaction => "Nouvelle question",
                PhaseQuestion::Lecture => "Réponses publiées",
                PhaseQuestion::Vote => "Votes ouverts",
                PhaseQuestion::Resultat => "Question terminée"
            };

            $donneesQuestions[] = [
                "idQuestion" => $question->getIdQuestion(),
                "titre" => $question->getTitre(),
                "description" => $question->getDescription(),
                "datePublication" => $question->getDateDebutRedaction() ? $question->getDateDebutRedaction()->format("d/m/Y") : "Non Publiée",
                "phase" => $phase->toString(),
                "nomUsuelOrganisateur" => $question->getOrganisateur()->getNomUsuel(),
                "pfp" => $question->getOrganisateur()->getPhotoProfil(),
                "estAVous" => $username == $question->getUsernameOrganisateur(),
                "statusQuestion" =>  $status
            ];

        }

        static::afficherVue("view.php", [
            "titrePage" => "Accueil",
            "contenuPage" => "accueil.php",
            "questions" => $donneesQuestions
        ]);
    }

    public static function getIfSet(string $parametre, string $errorUrl = ACCUEIL_URL, string $errorMessage = "[PARAMETRE] non rempli"): string
    {
        $errorMessage = str_replace("[PARAMETRE]", $parametre, $errorMessage);
        if (!isset($_GET[$parametre])) {
            if (!isset($_POST[$parametre])) {
                static::error($errorUrl, $errorMessage);
            } else {
                return $_POST[$parametre];
            }
        } else {
            return $_GET[$parametre];
        }
    }

    public static function getIfSetAndNumeric(string $parametre, string $errorUrl = ACCUEIL_URL, string $errorMessage = "[PARAMETRE] non rempli"): int
    {
        $valeur = static::getIfSet($parametre, $errorUrl, $errorMessage);
        if (!is_numeric($valeur)) {
            static::error($errorUrl, "$parametre doit être un nombre");
        }
        return (int) $valeur;
    }

    public static function getIfSetAndNotEmpty(string $parametre, string $errorUrl = ACCUEIL_URL, string $errorMessage = "[PARAMETRE] non rempli"): string
    {
        $valeur = static::getIfSet($parametre, $errorUrl, $errorMessage);
        if (empty($valeur)) {
            static::error($errorUrl, "$parametre ne peut pas être vide");
        }
        return $valeur;
    }
}
