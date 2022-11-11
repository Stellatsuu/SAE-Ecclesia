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

    public function getDemandes(): array
    {
        return UtilisateurRepository::getDemandesFaitesParUtilisateur($this->getIdUtilisateur());
    }
    
}
