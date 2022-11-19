<?php

namespace App\SAE\Model\DataObject;

use App\SAE\Model\Repository\UtilisateurRepository;
use JsonSerializable;

class Utilisateur extends AbstractDataObject implements JsonSerializable
{

    private int $idUtilisateur;

    private string $nom;

    private string $prenom;

    public function __construct(int $idUtilisateur, string $nom, string $prenom)
    {
        $this->idUtilisateur = $idUtilisateur;
        $this->nom = $nom;
        $this->prenom = $prenom;
    }

    public function formatTableau(): array
    {
        return [
            'nom_utilisateur' => $this->nom,
            'prenom_utilisateur' => $this->prenom
        ];
    }

    public function getValeurClePrimaire(): int
    {
        return $this->getIdUtilisateur();
    }
    
    // Getters

    public function getIdUtilisateur(): int
    {
        return $this->idUtilisateur;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'idUtilisateur' => $this->idUtilisateur,
            'nom' => $this->nom,
            'prenom' => $this->prenom
        ];
    }

    public static function toUtilisateur($object): Utilisateur
    {
        return $object;
    }
    
}
