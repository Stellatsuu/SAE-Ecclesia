<?php

namespace App\SAE\Controller;

use App\SAE\Lib\MessageFlash;
use App\SAE\Model\HTTP\Session;
use App\SAE\Model\Repository\DatabaseConnection;

const ACCUEIL_URL = "frontController.php";
const LDQ_URL = "frontController.php?controller=demandeQuestion&action=listerDemandesQuestion";
const AFDQ_URL = "frontController.php?controller=demandeQuestion&action=afficherFormulaireDemandeQuestion";
const LMQ_URL = "frontController.php?controller=question&action=listerMesQuestions";

class MainController
{
    public static function afficherVue(string $cheminVue, array $parametres): void
    {
        extract($parametres); // Crée des variables à partir du tableau $parametres
        require __DIR__ . '/../view/' . $cheminVue;
    }

    /**
     * Affiche le message $message sur la page associée à $action
     */
    public static function message(string $url, string $message): void
    {
        MessageFlash::ajouter("info", $message);
        static::redirect($url);
    }

    public static function error(string $url, string $message): void
    {
        MessageFlash::ajouter("error", $message);
        static::redirect($url);
    }

    public static function redirect(string $url): void
    {
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

        $pdo->exec($query1);
        $pdo->exec($query2);

        static::message(ACCUEIL_URL, "La base de données a été réinitialisée");
    }

    public static function seConnecter(): void
    {
        $session = Session::getInstance();

        $idUtilisateur = static::getIfSetAndNumeric("idUtilisateur");

        $session->enregistrer("idUtilisateur", $idUtilisateur);

        static::message(ACCUEIL_URL, "Désormais connecté en tant que $idUtilisateur");
    }

    protected static function getSessionSiConnecte($errorUrl = ACCUEIL_URL): Session
    {
        $session = Session::getInstance();
        if (!$session->contient("idUtilisateur")) {
            static::error($errorUrl, "Vous devez être connecté pour accéder à cette page");
        }
        return $session;
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
