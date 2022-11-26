<?php

namespace App\SAE\Model\DataObject;

use App\SAE\Model\Repository\DatabaseConnection;
use App\SAE\Lib\PhaseQuestion as Phase;
use App\SAE\Model\SystemeVote\AbstractSystemeVote;
use DateTime;
use JsonSerializable;

class Question extends DemandeQuestion implements JsonSerializable
{
    private ?array $sections;
    private ?array $responsables;
    private ?array $votants;

    private ?DateTime $dateDebutRedaction;
    private ?DateTime $dateFinRedaction;
    private ?DateTime $dateOuvertureVotes;
    private ?DateTime $dateFermetureVotes;
    private ?AbstractSystemeVote $systemeVote;

    public function __construct(int $idQuestion, string $titre, string $description, Utilisateur $organisateur, ?array $sections, ?array $responsables, ?array $votants, ?DateTime $dateDebutRedaction, ?DateTime $dateFinRedaction, ?DateTime $dateOuvertureVotes, ?DateTime $dateFermetureVotes)
    {
        parent::__construct($idQuestion, $titre, $description, $organisateur);
        $this->sections = $sections;
        $this->responsables = $responsables;
        $this->votants = $votants;
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
            'date_fermeture_votes' => $this->dateOuvertureVotes === null ? "" : $this->dateFermetureVotes->format('Y-m-d H:i:s'),
            'systeme_vote' => $this->systemeVote === null ? "" : $this->systemeVote->getNom()
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

    public function getResponsables(): ?array
    {
        return $this->responsables;
    }

    public function getVotants(): ?array
    {
        return $this->votants;
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

    public function getSystemeVote(): AbstractSystemeVote
    {
        return $this->systemeVote;
    }

    public function getPhase() {
        $now = new DateTime();
        if($this->dateDebutRedaction === null || $this->dateFinRedaction === null || $this->dateOuvertureVotes === null || $this->dateFermetureVotes === null) {
            return Phase::NonRemplie;
        }
            
        if($now < $this->dateDebutRedaction) {
            return Phase::Attente;
        }

        if($now >= $this->dateDebutRedaction && $now <= $this->dateFinRedaction) {
            return Phase::Redaction;
        }

        if($now > $this->dateFinRedaction && $now < $this->dateOuvertureVotes) {
            return Phase::Lecture;
        }

        if($now >= $this->dateOuvertureVotes && $now <= $this->dateFermetureVotes) {
            return Phase::Vote;
        }

        if($now > $this->dateFermetureVotes) {
            return Phase::Resultat;
        }
    }

    //Setters

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setSections(?array $sections): void
    {
        $this->sections = $sections;
    }

    public function setResponsables(?array $responsables): void
    {
        $this->responsables = $responsables;
    }

    public function setVotants(?array $votants): void
    {
        $this->votants = $votants;
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

    public function setSystemeVote(?AbstractSystemeVote $systemeVote): void
    {
        $this->systemeVote = $systemeVote;
    }

    public static function toQuestion($object) : Question {
        return $object;
    }

    public function jsonSerialize() : mixed
    {
        return [
            'id_question' => $this->getIdQuestion(),
            'titre_question' => $this->getTitre(),
            'description_question' => $this->getDescription(),
            'organisateur' => $this->getOrganisateur(),
            'sections' => $this->getSections(),
            'responsables' => $this->getResponsables(),
            'votants' => $this->getVotants(),
            'date_debut_redaction' => $this->getDateDebutRedaction(),
            'date_fin_redaction' => $this->getDateFinRedaction(),
            'date_ouverture_votes' => $this->getDateOuvertureVotes(),
            'date_fermeture_votes' => $this->getDateFermetureVotes(),
            'systeme_vote' => $this->getSystemeVote()
        ];
    }
}
