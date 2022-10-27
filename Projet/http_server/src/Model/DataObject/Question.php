<?php

namespace App\SAE\Model\DataObject;

use DateTime;

class Question extends DemandeQuestion
{
    private array $sections;

    private DateTime $dateDebutRedaction;
    private DateTime $dateFinRedaction;
    private DateTime $dateOuvertureVotes;
    private DateTime $dateFinVotes;

    public function __construct(int $idQuestion, string $titre, string $intitule, Utilisateur $organisateur, ?array $sections, ?DateTime $dateDebutRedaction, ?DateTime $dateFinRedaction, ?DateTime $dateOuvertureVotes, ?DateTime $dateFinVotes)
    {
        parent::__construct($idQuestion, $titre, $intitule, $organisateur);
        $this->sections = $sections;
        $this->dateDebutRedaction = $dateDebutRedaction;
        $this->dateFinRedaction = $dateFinRedaction;
        $this->dateOuvertureVotes = $dateOuvertureVotes;
        $this->dateFinVotes = $dateFinVotes;
    }

    public function formatTableau(): array
    {
        return [
            'titre' => $this->getTitre(),
            'intitule' => $this->getIntitule(),
            'idutilisateur' => $this->getOrganisateur()->getIdUtilisateur(),
            'datedebutredaction' => $this->dateDebutRedaction->format('Y-m-d H:i:s'),
            'datefinredaction' => $this->dateFinRedaction->format('Y-m-d H:i:s'),
            'dateouverturevotes' => $this->dateOuvertureVotes->format('Y-m-d H:i:s'),
            'datefinvotes' => $this->dateFinVotes->format('Y-m-d H:i:s')
        ];
    }

    public function getValeurClePrimaire(): int
    {
        return $this->getIdQuestion();
    }

    
}
