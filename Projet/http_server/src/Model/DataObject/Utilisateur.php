<?php

namespace App\SAE\Model\DataObject;

use App\SAE\Model\Repository\UtilisateurRepository;

class Utilisateur extends AbstractDataObject
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
            'idutilisateur' => $this->idUtilisateur,
            'nom' => $this->nom,
            'prenom' => $this->prenom
        ];
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
