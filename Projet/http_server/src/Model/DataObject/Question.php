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

    public function __construct(int $idQuestion, string $titre, string $description, Utilisateur $organisateur, ?array $sections, ?DateTime $dateDebutRedaction, ?DateTime $dateFinRedaction, ?DateTime $dateOuvertureVotes, ?DateTime $dateFermetureVotes)
    {
        parent::__construct($idQuestion, $titre, $description, $organisateur);
        $this->sections = $sections;
        $this->dateDebutRedaction = $dateDebutRedaction;
        $this->dateFinRedaction = $dateFinRedaction;
        $this->dateOuvertureVotes = $dateOuvertureVotes;
        $this->dateFermetureVotes = $dateFermetureVotes;
    }

    public function formatTableau(): array
    {
        return [
            'titre_question' => $this->getTitre(),
            'description_question' => $this->getDescription(),
            'id_organisateur' => $this->getOrganisateur()->getIdUtilisateur(),
            'date_debut_redaction' => $this->dateDebutRedaction === null ? "" : $this->dateDebutRedaction->format('Y-m-d H:i:s'),
            'date_fin_redaction' => $this->dateFinRedaction === null ? "" : $this->dateFinRedaction ->format('Y-m-d H:i:s'),
            'date_ouverture_votes' => $this->dateOuvertureVotes === null ? "" : $this->dateOuvertureVotes->format('Y-m-d H:i:s'),
            'date_fermeture_votes' => $this->dateOuvertureVotes === null ? "" : $this->dateFermetureVotes->format('Y-m-d H:i:s')
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

    //Setters

    public function setSections(?array $sections): void
    {
        $this->sections = $sections;
    }

    public function setDateDebutRedaction(?DateTime $dateDebutRedaction): void
    {
        $this->dateDebutRedaction = $dateDebutRedaction;
    }

    public function setDateFinRedaction(?DateTime $dateFinRedaction): void
    {
        $this->dateFinRedaction = $dateFinRedaction;
    }

    public function setDateOuvertureVotes(?DateTime $dateOuvertureVotes): void
    {
        $this->dateOuvertureVotes = $dateOuvertureVotes;
    }

    public function setDateFermetureVotes(?DateTime $dateFermetureVotes): void
    {
        $this->dateFermetureVotes = $dateFermetureVotes;
    }

    public static function toQuestion($object) : Question {
        return $object;
    }

    
}
