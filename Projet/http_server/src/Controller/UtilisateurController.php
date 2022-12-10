<?php
namespace App\SAE\Controller;

use App\SAE\Lib\ConnexionUtilisateur;
use App\SAE\Lib\MotDePasse;
use App\SAE\Model\DataObject\Utilisateur;
use App\SAE\Model\Repository\UtilisateurRepository;

class UtilisateurController extends MainController
{
   
    public static function seConnecter()
    {
        $redirect = isset($_POST["redirect"]) ? $_POST["redirect"] : ACCUEIL_URL;
        $username = static::getIfSetAndNotEmpty("username");
        $password = static::getIfSetAndNotEmpty("password");

        if(ConnexionUtilisateur::estConnecte()) {
            static::message($redirect, "Vous êtes déjà connecté");
            return;
        }

        $utilisateur = Utilisateur::castIfNotNull((new UtilisateurRepository)->select($username), $redirect, "Le nom d'utilisateur ou le mot de passe est incorrect");
        
        $verifie = MotDePasse::verifier($password, $utilisateur->getMdpHashed());

        if ($verifie) {
            ConnexionUtilisateur::connecter($username);
            static::redirect($redirect);
        } else {
            static::error($redirect, "Le nom d'utilisateur ou le mot de passe est incorrect");
        }
    }

    public static function seDeconnecter()
    {
        ConnexionUtilisateur::deconnecter();
        static::message(ACCUEIL_URL, "Vous êtes déconnecté");
    }

}
