<?php

namespace App\SAE\Controller;

use App\SAE\Lib\MessageFlash;
use App\SAE\Lib\PhotoProfil;
use App\SAE\Model\HTTP\Session;
use App\SAE\Model\Repository\DatabaseConnection;
use App\SAE\Model\Repository\UtilisateurRepository;

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
const LMQ_URL = "frontController.php?controller=question&action=listerMesQuestions";

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
        static::afficherVue("view.php", [
            "titrePage" => "Accueil",
            "contenuPage" => "accueil.php"
        ]);
    }

    protected static function getIfSet(string $parametre, string $errorUrl = ACCUEIL_URL, string $errorMessage = "[PARAMETRE] non rempli"): string
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

    protected static function getIfSetAndNumeric(string $parametre, string $errorUrl = ACCUEIL_URL, string $errorMessage = "[PARAMETRE] non rempli"): int
    {
        $valeur = static::getIfSet($parametre, $errorUrl, $errorMessage);
        if (!is_numeric($valeur)) {
            static::error($errorUrl, "$parametre doit être un nombre");
        }
        return (int) $valeur;
    }

    protected static function getIfSetAndNotEmpty(string $parametre, string $errorUrl = ACCUEIL_URL, string $errorMessage = "[PARAMETRE] non rempli"): string
    {
        $valeur = static::getIfSet($parametre, $errorUrl, $errorMessage);
        if (empty($valeur)) {
            static::error($errorUrl, "$parametre ne peut pas être vide");
        }
        return $valeur;
    }
}
