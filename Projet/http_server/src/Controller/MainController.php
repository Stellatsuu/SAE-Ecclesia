<?php

namespace App\SAE\Controller;

use App\SAE\Lib\MessageFlash;
use App\SAE\Model\HTTP\Session;
use App\SAE\Model\Repository\DatabaseConnection;

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
    public static function message(string $action, string $message): void
    {
        MessageFlash::ajouter("info", $message);
        static::$action();
    }

    public static function error(string $action, string $message): void
    {
        MessageFlash::ajouter("error", $message);
        static::$action();
    }

    public static function redirect(string $url): void
    {

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

        $pdo->exec($query1);
        $pdo->exec($query2);

        static::message("afficherAccueil", "La base de données a été réinitialisée");
    }

    public static function seConnecter(): void
    {
        $session = Session::getInstance();

        $idUtilisateur = $_GET['idUtilisateur'];

        $session->enregistrer("idUtilisateur", $idUtilisateur);
    }
}
