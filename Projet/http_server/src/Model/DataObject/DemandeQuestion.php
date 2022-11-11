<?php

namespace App\SAE\Model\DataObject;

class DemandeQuestion extends AbstractDataObject
{

    private int $idQuestion;

    private string $titre;

    private string $description;

    private Utilisateur $organisateur;

    public function __construct(int $idQuestion, string $titre, string $description, Utilisateur $organisateur)
    {
        $this->idQuestion = $idQuestion;
        $this->titre = $titre;
        $this->description = $description;
        $this->organisateur = $organisateur;
    }

    public function formatTableau(): array
    {
        return [
            'titre_demande_question' => $this->titre,
            'description_demande_question' => $this->description,
            'id_organisateur' => $this->organisateur->getIdUtilisateur()
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getOrganisateur(): Utilisateur
    {
        return $this->organisateur;
    }

    public static function toDemandeQuestion($object): DemandeQuestion
    {
        return $object;
    }
    
}
