<?php
namespace App\SAE\Controller;

use App\SAE\Lib\ConnexionUtilisateur;
use App\SAE\Lib\MotDePasse;
use App\SAE\Lib\PhotoProfil;
use App\SAE\Model\DataObject\Utilisateur;
use App\SAE\Model\Repository\UtilisateurRepository;

class UtilisateurController extends MainController
{

    public static function sInscrire() {
        $redirect = isset($_POST["redirect"]) ? $_POST["redirect"] : ACCUEIL_URL;
        $redirectErreur = $redirect . "#modalCreerCompte";
        $username = static::getIfSetAndNotEmpty("username", $redirectErreur, "Un nom d'utilisateur est requis");
        $password = static::getIfSetAndNotEmpty("password", $redirectErreur, "Un mot de passe est requis");
        $passwordConfirmation = static::getIfSetAndNotEmpty("passwordConfirmation", $redirectErreur, "La confirmation du mot de passe est requise");
        $nom = isset($_POST["nom"]) ? $_POST["nom"] : null;
        $prenom = isset($_POST["prenom"]) ? $_POST["prenom"] : null;
        $email = isset($_POST["email"]) ? $_POST["email"] : null;

        if(ConnexionUtilisateur::estConnecte()) {
            static::message($redirect, "Vous êtes déjà connecté");
            return;
        }

        $existsUsername = (new UtilisateurRepository)->select($username);
        if ($existsUsername) {
            static::error($redirectErreur, "Ce nom d'utilisateur est déjà pris");
        }

        if ($password !== $passwordConfirmation) {
            static::error($redirectErreur, "Les mots de passe ne correspondent pas");
        }

        $secure = MotDePasse::verifierForceMotDePasse($password);
        if (!$secure) {
            static::error($redirectErreur, "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial");
        }

        if($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            static::error($redirectErreur, "L'adresse email n'est pas valide");
        }

        if(strlen($username) > 50) {
            static::error($redirectErreur, "Le nom d'utilisateur ne doit pas dépasser 50 caractères");
        }

        if($nom && strlen($nom) > 50) {
            static::error($redirectErreur, "Le nom ne doit pas dépasser 50 caractères");
        }

        if($prenom && strlen($prenom) > 50) {
            static::error($redirectErreur, "Le prénom ne doit pas dépasser 50 caractères");
        }

        if($email && strlen($email) > 100) {
            static::error($redirectErreur, "L'adresse email ne doit pas dépasser 100 caractères");
        }

        $utilisateur = new Utilisateur(
            $username,
            $nom,
            $prenom,
            $email,
            PhotoProfil::getRandomPhotoProfilParDefaut(),
            MotDePasse::hacher($password)
        );

        (new UtilisateurRepository)->insert($utilisateur);

        ConnexionUtilisateur::connecter($username);
        static::message($redirect, "Votre compte a été créé");
    }
   
    public static function seConnecter()
    {
        $redirect = isset($_POST["redirect"]) ? $_POST["redirect"] : ACCUEIL_URL;
        $redirectErreur = $redirect . "#modalSeConnecter";
        $username = static::getIfSetAndNotEmpty("username", $redirectErreur, "Le nom d'utilisateur ou le mot de passe est incorrect");
        $password = static::getIfSetAndNotEmpty("password", $redirectErreur, "Le nom d'utilisateur ou le mot de passe est incorrect");

        if(ConnexionUtilisateur::estConnecte()) {
            static::message($redirect, "Vous êtes déjà connecté");
            return;
        }

        $utilisateur = Utilisateur::castIfNotNull((new UtilisateurRepository)->select($username), $redirectErreur, "Le nom d'utilisateur ou le mot de passe est incorrect");
        
        $verifie = MotDePasse::verifier($password, $utilisateur->getMdpHashed());

        if ($verifie) {
            ConnexionUtilisateur::connecter($username);
            static::redirect($redirect);
        } else {
            static::error($redirectErreur, "Le nom d'utilisateur ou le mot de passe est incorrect");
        }
    }

    public static function seDeconnecter()
    {
        ConnexionUtilisateur::deconnecter();
        static::message(ACCUEIL_URL, "Vous êtes déconnecté");
    }

}
