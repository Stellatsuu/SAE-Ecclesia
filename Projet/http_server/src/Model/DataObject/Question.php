<?php

namespace App\SAE\Model\DataObject;

use DateTime;

class Question extends DemandeQuestion
{
    private ?array $sections;

    private ?DateTime $dateDebutRedaction;
    private ?DateTime $dateFinRedaction;
    private ?DateTime $dateOuvertureVotes;
    private ?DateTime $dateFermetureVotes;

    public function __construct(int $idQuestion, string $titre, string $intitule, Utilisateur $organisateur, ?array $sections, ?DateTime $dateDebutRedaction, ?DateTime $dateFinRedaction, ?DateTime $dateOuvertureVotes, ?DateTime $dateFermetureVotes)
    {
        parent::__construct($idQuestion, $titre, $intitule, $organisateur);
        $this->sections = $sections;
        $this->dateDebutRedaction = $dateDebutRedaction;
        $this->dateFinRedaction = $dateFinRedaction;
        $this->dateOuvertureVotes = $dateOuvertureVotes;
        $this->dateFermetureVotes = $dateFermetureVotes;
    }

    public function formatTableau(): array
    {
        return [
            'titre' => $this->getTitre(),
            'intitule' => $this->getIntitule(),
            'idutilisateur' => $this->getOrganisateur()->getIdUtilisateur(),
            'datedebutredaction' => $this->dateDebutRedaction === null ? "" : $this->dateDebutRedaction->format('Y-m-d H:i:s'),
            'datefinredaction' => $this->dateFinRedaction === null ? "" : $this->dateFinRedaction ->format('Y-m-d H:i:s'),
            'dateouverturevotes' => $this->dateOuvertureVotes === null ? "" : $this->dateOuvertureVotes->format('Y-m-d H:i:s'),
            'datefermeturevotes' => $this->dateOuvertureVotes === null ? "" : $this->dateFermetureVotes->format('Y-m-d H:i:s')
        ];
    }

    public function getValeurClePrimaire(): int
    {
        return $this->getIdQuestion();
    }

    //Getters
    public function getSections(): ?array
    {
        return $this->sections;
    }

    public function getDateDebutRedaction(): ?DateTime
    {
        return $this->dateDebutRedaction;
    }

    public function getDateFinRedaction(): ?DateTime
    {
        return $this->dateFinRedaction;
    }

    public function getDateOuvertureVotes(): ?DateTime
    {
        return $this->dateOuvertureVotes;
    }

    public function getDateFermetureVotes(): ?DateTime
    {
        return $this->dateFermetureVotes;
    }

    
}
