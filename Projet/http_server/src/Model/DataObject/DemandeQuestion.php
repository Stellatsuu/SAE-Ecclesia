<?php

namespace App\SAE\Model\DataObject;

use App\SAE\Model\Repository\UtilisateurRepository;

class DemandeQuestion extends AbstractDataObject
{

    private int $idQuestion;
    private string $titre;
    private string $description;

    private ?Utilisateur $organisateur;

    private int $idOrganisateur;

    public function __construct(int $idQuestion, string $titre, string $description, Utilisateur $organisateur)
    {
        $this->idQuestion = $idQuestion;
        $this->titre = $titre;
        $this->description = $description;
        $this->organisateur = $organisateur;

        $this->idOrganisateur = $organisateur->getIdUtilisateur();
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
        if ($this->organisateur === null) {
            $this->organisateur = (new UtilisateurRepository())->select($this->idOrganisateur);
        }
        return $this->organisateur;
    }

    public function getIdOrganisateur(): int
    {
        return $this->idOrganisateur;
    }

    public static function toDemandeQuestion($object): DemandeQuestion
    {
        return $object;
    }
    
}
