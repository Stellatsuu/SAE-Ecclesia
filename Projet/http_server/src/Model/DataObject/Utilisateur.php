<?php

namespace App\SAE\Model\DataObject;

use App\SAE\Lib\PhotoProfil;
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

    public function __construct(string $username, ?string $nom, ?string $prenom, ?string $email, string $mdpHashed)
    {
        $this->username = $username;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->mdpHashed = $mdpHashed;

        $this->photoProfil = null;
    }

    public function formatTableau(): array
    {
        return [
            'username_utilisateur' => $this->username,
            'nom_utilisateur' => $this->nom,
            'prenom_utilisateur' => $this->prenom,
            'email_utilisateur' => $this->email,
            'photo_profil' => $this->getPhotoProfil(),
            'mdp_hashed' => $this->mdpHashed
        ];
    }

    public function getValeurClePrimaire(): string
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

    public function getNomUsuel(): string
    {
        if($this->nom == "" || $this->prenom == "") {
            return $this->username;
        } else {
            return $this->prenom . " " . $this->nom . " (" . $this->username . ")";
        }
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhotoProfil($resolution = 256): ?string
    {
        if($this->photoProfil == null) {
            $this->photoProfil = (new UtilisateurRepository)->selectPhotoProfil($this->username);
        }

        if($resolution == 256) {
            return $this->photoProfil;
        } else {
            return PhotoProfil::convertirRedimensionnerRogner($this->photoProfil, $resolution);
        }
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

    // Setters

    public function setPhotoProfil(?string $photoProfil): void
    {
        $this->photoProfil = $photoProfil;
    }

    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    public function setPrenom(?string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function setMdpHashed(string $mdpHashed): void
    {
        $this->mdpHashed = $mdpHashed;
    }

    public static function castIfNotNull($object, $errorUrl = "frontController.php", $errorMessage = "[OBJECT] n'existe pas"): Utilisateur
    {
        return static::castToClassIfNotNull($object, Utilisateur::class, $errorUrl, $errorMessage);
    }
    
}
