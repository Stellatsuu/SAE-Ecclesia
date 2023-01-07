<?php

namespace App\SAE\Controller;

use App\SAE\Lib\ConnexionUtilisateur;
use App\SAE\Lib\MotDePasse;
use App\SAE\Lib\PhotoProfil;
use App\SAE\Model\DataObject\Utilisateur;
use App\SAE\Model\Repository\UtilisateurRepository;

const PROFIL_URL = "frontController.php?controller=utilisateur&action=afficherProfil";

class UtilisateurController extends MainController
{

    public static function sInscrire()
    {
        $redirect = isset($_POST["redirect"]) ? $_POST["redirect"] : ACCUEIL_URL;
        $redirectErreur = $redirect . "#modalCreerCompte";
        $username = static::getIfSetAndNotEmpty("username", $redirectErreur, "Un nom d'utilisateur est requis");
        $password = static::getIfSetAndNotEmpty("password", $redirectErreur, "Un mot de passe est requis");
        $passwordConfirmation = static::getIfSetAndNotEmpty("passwordConfirmation", $redirectErreur, "La confirmation du mot de passe est requise");
        $nom = isset($_POST["nom"]) ? $_POST["nom"] : null;
        $prenom = isset($_POST["prenom"]) ? $_POST["prenom"] : null;
        $email = isset($_POST["email"]) ? $_POST["email"] : null;

        if (ConnexionUtilisateur::estConnecte()) {
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

        if(strlen($password) > 100) {
            static::error($redirectErreur, "Le mot de passe ne doit pas dépasser 100 caractères");
        }

        $secure = MotDePasse::verifierForceMotDePasse($password);
        if (!$secure) {
            static::error($redirectErreur, "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial");
        }

        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            static::error($redirectErreur, "L'adresse email n'est pas valide");
        }

        if (strlen($username) > 50) {
            static::error($redirectErreur, "Le nom d'utilisateur ne doit pas dépasser 50 caractères");
        }

        if ($nom && strlen($nom) > 50) {
            static::error($redirectErreur, "Le nom ne doit pas dépasser 50 caractères");
        }

        if ($prenom && strlen($prenom) > 50) {
            static::error($redirectErreur, "Le prénom ne doit pas dépasser 50 caractères");
        }

        if ($nom && !$prenom || !$nom && $prenom) {
            static::error($redirectErreur, "Le nom et le prénom doivent être renseignés ensemble");
        }

        if ($email && strlen($email) > 100) {
            static::error($redirectErreur, "L'adresse email ne doit pas dépasser 100 caractères");
        }

        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            static::error($redirectErreur, "Le nom d'utilisateur ne doit contenir que des lettres, des chiffres, des tirets et des underscores");
        }

        $utilisateur = new Utilisateur(
            $username,
            $nom,
            $prenom,
            $email,
            MotDePasse::hacher($password)
        );

        $utilisateur->setPhotoProfil(PhotoProfil::getRandomPhotoProfilParDefaut());

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

        if (ConnexionUtilisateur::estConnecte()) {
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

    public static function afficherProfil()
    {
        $username = ConnexionUtilisateur::getUsernameSiConnecte();

        $utilisateur = Utilisateur::castIfNotNull((new UtilisateurRepository)->select($username));

        static::afficherVue("view.php", [
            "utilisateur" => $utilisateur,
            "contenuPage" => "afficherProfil.php",
            "titrePage" => "Mon compte"
        ]);
    }

    public static function modifierPFP()
    {
        $username = ConnexionUtilisateur::getUsernameSiConnecte();

        $utilisateur = Utilisateur::castIfNotNull((new UtilisateurRepository)->select($username));

        if (!isset($_FILES['userfile'])) {
            static::error(PROFIL_URL, "Erreur lors de l'envoi de l'image");
        }

        $image = file_get_contents($_FILES['userfile']['tmp_name']);
        $image = base64_encode($image);

        $image = PhotoProfil::convertirRedimensionnerRogner($image);

        $utilisateur->setPhotoProfil($image);
        (new UtilisateurRepository)->update($utilisateur);
        static::message(PROFIL_URL, "Votre photo de profil a été modifiée");
    }

    public static function modifierNomPrenom()
    {
        $username = ConnexionUtilisateur::getUsernameSiConnecte();

        $utilisateur = Utilisateur::castIfNotNull((new UtilisateurRepository)->select($username));

        $nom = static::getIfSetAndNotEmpty("nom", PROFIL_URL, "Le nom ne doit pas être vide");
        $prenom = static::getIfSetAndNotEmpty("prenom", PROFIL_URL, "Le prénom ne doit pas être vide");

        if (strlen($nom) > 50) {
            static::error(PROFIL_URL, "Le nom ne doit pas dépasser 50 caractères");
        }

        if (strlen($prenom) > 50) {
            static::error(PROFIL_URL, "Le prénom ne doit pas dépasser 50 caractères");
        }

        if ($nom && !$prenom || !$nom && $prenom) {
            static::error(PROFIL_URL, "Le nom et le prénom doivent être renseignés ensemble");
        }

        $utilisateur->setNom($nom);
        $utilisateur->setPrenom($prenom);
        (new UtilisateurRepository)->update($utilisateur);
        static::message(PROFIL_URL, "Votre nom et prénom ont été modifiés");
    }

    public static function modifierEmail()
    {
        $username = ConnexionUtilisateur::getUsernameSiConnecte();

        $utilisateur = Utilisateur::castIfNotNull((new UtilisateurRepository)->select($username));

        $email = static::getIfSetAndNotEmpty("email", PROFIL_URL, "L'adresse email ne doit pas être vide");

        if (strlen($email) > 100) {
            static::error(PROFIL_URL, "L'adresse email ne doit pas dépasser 100 caractères");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            static::error(PROFIL_URL, "L'adresse email n'est pas valide");
        }

        $utilisateur->setEmail($email);
        (new UtilisateurRepository)->update($utilisateur);
        static::message(PROFIL_URL, "Votre adresse email a été modifiée");
    }

    public static function modifierMDP()
    {
        $username = ConnexionUtilisateur::getUsernameSiConnecte();

        $utilisateur = Utilisateur::castIfNotNull((new UtilisateurRepository)->select($username));

        $ancienMDP = static::getIfSetAndNotEmpty("ancienMDP", PROFIL_URL, "L'ancien mot de passe ne doit pas être vide");
        $nouveauMDP = static::getIfSetAndNotEmpty("nouveauMDP", PROFIL_URL, "Le nouveau mot de passe ne doit pas être vide");
        $nouveauMDP2 = static::getIfSetAndNotEmpty("nouveauMDP2", PROFIL_URL, "La confirmation du nouveau mot de passe ne doit pas être vide");

        if (strlen($nouveauMDP) > 100) {
            static::error(PROFIL_URL, "Le nouveau mot de passe ne doit pas dépasser 100 caractères");
        }

        if (strlen($nouveauMDP2) > 100) {
            static::error(PROFIL_URL, "La confirmation du nouveau mot de passe ne doit pas dépasser 100 caractères");
        }

        if ($nouveauMDP != $nouveauMDP2) {
            static::error(PROFIL_URL, "Les deux mots de passe ne correspondent pas");
        }

        $secure = MotDePasse::verifierForceMotDePasse($nouveauMDP);
        if (!$secure) {
            static::error(PROFIL_URL, "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial");
        }

        $verifie = MotDePasse::verifier($ancienMDP, $utilisateur->getMdpHashed());
        if (!$verifie) {
            static::error(PROFIL_URL, "L'ancien mot de passe est incorrect");
        }

        $nouveauMDPHashed = MotDePasse::hacher($nouveauMDP);
        $utilisateur->setMdpHashed($nouveauMDPHashed);
        (new UtilisateurRepository)->update($utilisateur);

        static::message(PROFIL_URL, "Votre mot de passe a été modifié");
    }

    public static function supprimerCompte()
    {
        $username = ConnexionUtilisateur::getUsernameSiConnecte();

        $utilisateur = Utilisateur::castIfNotNull((new UtilisateurRepository)->select($username));

        $mdp = static::getIfSetAndNotEmpty("mdp_suppression", PROFIL_URL, "Le mot de passe ne doit pas être vide");

        $verifie = MotDePasse::verifier($mdp, $utilisateur->getMdpHashed());
        if (!$verifie) {
            static::error(PROFIL_URL, "Le mot de passe est incorrect");
        }

        (new UtilisateurRepository)->delete($utilisateur->getUsername());
        ConnexionUtilisateur::deconnecter();
        static::message(ACCUEIL_URL, "Votre compte a été supprimé");
    }
}
