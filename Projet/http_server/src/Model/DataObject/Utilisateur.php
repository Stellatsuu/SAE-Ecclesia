<?php

namespace App\SAE\Model\DataObject;

use App\SAE\Model\Repository\UtilisateurRepository;

class Utilisateur
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

    public function getQuestionsPosees(): array
    {
        return UtilisateurRepository::getQuestionsPoseesParUtilisateur($this->getIdUtilisateur());
    }
    
}
