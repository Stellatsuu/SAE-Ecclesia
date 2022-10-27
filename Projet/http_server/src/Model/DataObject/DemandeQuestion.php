<?php

namespace App\SAE\Model\DataObject;

class DemandeQuestion extends AbstractDataObject
{

    private int $idQuestion;

    private string $titre;

    private string $intitule;

    private Utilisateur $organisateur;

    public function __construct(int $idQuestion, string $titre, string $intitule, Utilisateur $organisateur)
    {
        $this->idQuestion = $idQuestion;
        $this->titre = $titre;
        $this->intitule = $intitule;
        $this->organisateur = $organisateur;
    }

    public function formatTableau(): array
    {
        return [
            'titre' => $this->titre,
            'intitule' => $this->intitule,
            'idutilisateur' => $this->organisateur->getIdUtilisateur()
        ];
    }

    public function getValeurClePrimaire(): int
    {
        return $this->getIdQuestion();
    }

    // Getters
    
    public function getIdQuestion(): int
    {
        return $this->idQuestion;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function getIntitule(): string
    {
        return $this->intitule;
    }

    public function getOrganisateur(): Utilisateur
    {
        return $this->organisateur;
    }
    
}
