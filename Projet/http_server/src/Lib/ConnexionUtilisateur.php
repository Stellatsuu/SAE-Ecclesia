<?php

namespace App\SAE\Lib;

use App\SAE\Controller\MainController;
use App\SAE\Model\HTTP\Session;

use const App\SAE\Controller\ACCUEIL_URL;

class ConnexionUtilisateur
{
    // L'utilisateur connecté sera enregistré en session associé à la clé suivante 
    private static string $cleConnexion = "username";

    public static function connecter(string $username): void
    {
        $session = Session::getInstance();
        $session->enregistrer(self::$cleConnexion, $username);
    }

    public static function estConnecte(): bool
    {
        $session = Session::getInstance();
        return $session->contient(self::$cleConnexion);
    }

    public static function deconnecter(): void
    {
        $session = Session::getInstance();
        $session->supprimer(self::$cleConnexion);
    }

    public static function getUsername(): ?string
    {
        $session = Session::getInstance();
        if ($session->contient(self::$cleConnexion)) {
            return $session->lire(self::$cleConnexion);
        } else {
            return null;
        }
    }

    public static function getUsernameSiConnecte($errorURL = ACCUEIL_URL): ?string
    {
        if (self::estConnecte()) {
            return self::getUsername();
        } else {
            MainController::error($errorURL, "Vous devez être connecté pour accéder à cette page");
        }
    }
}
