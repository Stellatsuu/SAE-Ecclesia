<?php

namespace App\SAE\Controller;

use App\SAE\Lib\MessageFlash;
use App\SAE\Model\HTTP\Session;
use App\SAE\Model\Repository\DatabaseConnection;

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
        if(static::$isTesting) return;

        extract($parametres); // Crée des variables à partir du tableau $parametres
        require __DIR__ . '/../view/' . $cheminVue;
    }

    public static function message(string $url, string $message): void
    {
        if (!static::$isTesting) {
            MessageFlash::ajouter("info", $message);
            static::redirect($url);
        } else {
            static::logToFile("info: " . $message);
        }
    }

    public static function error(string $url, string $message): void
    {
        if (!static::$isTesting) {
            MessageFlash::ajouter("error", $message);
            static::redirect($url);
        } else {
            static::logToFile("error: " . $message);
            throw new \Exception($message);
        }
    }

    public static function redirect(string $url): void
    {
        if(static::$isTesting) return;

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

    public static function resetDatabase(): void
    {
        $pdo = DatabaseConnection::getPdo();
        $query1 = file_get_contents(__DIR__ . "/../../../scriptCreationTables.sql");
        $query2 = file_get_contents(__DIR__ . "/../../../jeuDeDonnées.sql");

        //set the pdo in warning mode
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);


        //get the drop statements from $query1
        $dropStatements = [];
        $statements = explode(";", $query1);
        foreach ($statements as $statement) {
            if (strpos($statement, "DROP") !== false) {
                $dropStatements[] = $statement;
            }
        }

        //remove the drop statements from $query1
        $query1 = str_replace($dropStatements, "", $query1);

        //execute the drop statements
        foreach ($dropStatements as $dropStatement) {
            $pdo->exec($dropStatement);
        }

        //set the pdo in exception mode
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $pdo->exec($query1);
        $pdo->exec($query2);

        static::message(ACCUEIL_URL, "La base de données a été réinitialisée");
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

    public static function logToFile(string $message): void
    {
        $date = date("Y-m-d H:i:s");
        $message = "$date : $message";
        file_put_contents(__DIR__ . "/../../../log.txt", $message . PHP_EOL, FILE_APPEND);
    }

    public static function clearLogFile(): void
    {
        file_put_contents(__DIR__ . "/../../../log.txt", "");
    }
}
