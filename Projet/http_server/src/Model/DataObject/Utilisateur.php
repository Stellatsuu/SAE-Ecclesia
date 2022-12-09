<?php

namespace App\SAE\Model\DataObject;

use App\SAE\Model\Repository\UtilisateurRepository;
use JsonSerializable;

class Utilisateur extends AbstractDataObject implements JsonSerializable
{

    private string $username;

    private ?string $nom;

    private ?string $prenom;

    private ?string $email;

    private ?string $photoProfil;

    private string $mdpHashed;

    public function __construct(string $username, ?string $nom, ?string $prenom, ?string $email, ?string $photoProfil, string $mdpHashed)
    {
        $this->username = $username;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->photoProfil = $photoProfil;
        $this->mdpHashed = $mdpHashed;
    }

    public function formatTableau(): array
    {
        return [
            'username_utilisateur' => $this->username,
            'nom_utilisateur' => $this->nom,
            'prenom_utilisateur' => $this->prenom,
            'email_utilisateur' => $this->email,
            'photo_profil' => $this->photoProfil,
            'mdp_hashed' => $this->mdpHashed
        ];
    }

    public function getValeurClePrimaire(): int
    {
        return $this->getUsername();
    }
    
    // Getters

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhotoProfil(): string
    {
        return $this->photoProfil;
    }

    public function getMdpHashed(): string
    {
        return $this->mdpHashed;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'username' => $this->username,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'photoProfil' => $this->photoProfil,
        ];
    }

    public static function castIfNotNull($object, $errorUrl = "frontController.php", $errorMessage = "[OBJECT] n'existe pas"): Utilisateur
    {
        return static::castToClassIfNotNull($object, Utilisateur::class, $errorUrl, $errorMessage);
    }
    
}
