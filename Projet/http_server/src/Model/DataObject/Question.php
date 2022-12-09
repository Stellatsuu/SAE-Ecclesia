<?php

namespace App\SAE\Model\DataObject;

use App\SAE\Model\Repository\DatabaseConnection;
use App\SAE\Lib\PhaseQuestion as Phase;
use App\SAE\Model\Repository\QuestionRepository;
use App\SAE\Model\Repository\RedacteurRepository;
use App\SAE\Model\Repository\SectionRepository;
use App\SAE\Model\Repository\UtilisateurRepository;
use App\SAE\Model\Repository\VotantRepository;
use App\SAE\Model\SystemeVote\AbstractSystemeVote;
use DateTime;
use JsonSerializable;

class Question extends DemandeQuestion implements JsonSerializable
{
    private array $sections;
    private array $redacteurs;
    private array $votants;

    private ?DateTime $dateDebutRedaction;
    private ?DateTime $dateFinRedaction;
    private ?DateTime $dateOuvertureVotes;
    private ?DateTime $dateFermetureVotes;
    private ?AbstractSystemeVote $systemeVote;

    public function __construct(int $idQuestion, string $titre, string $description, Utilisateur|string $organisateur)
    {
        parent::__construct($idQuestion, $titre, $description, $organisateur);

        $this->sections = [];
        $this->redacteurs = [];
        $this->votants = [];

        $this->dateDebutRedaction = null;
        $this->dateFinRedaction = null;
        $this->dateOuvertureVotes = null;
        $this->dateFermetureVotes = null;
        $this->systemeVote = null;
    }

    //Respect du contrat

    public function formatTableau(): array
    {
        return [
            'titre_question' => $this->getTitre(),
            'description_question' => $this->getDescription(),
            'username_organisateur' => $this->getUsernameOrganisateur(),
            'date_debut_redaction' => $this->dateDebutRedaction == null ? null : $this->dateDebutRedaction->format('Y-m-d H:i:s'),
            'date_fin_redaction' => $this->dateFinRedaction == null ? null : $this->dateFinRedaction ->format('Y-m-d H:i:s'),
            'date_ouverture_votes' => $this->dateOuvertureVotes == null ? null : $this->dateOuvertureVotes->format('Y-m-d H:i:s'),
            'date_fermeture_votes' => $this->dateOuvertureVotes == null ? null : $this->dateFermetureVotes->format('Y-m-d H:i:s'),
            'systeme_vote' => $this->systemeVote == null ? "" : $this->systemeVote->getNom()
        ];
    }

    public function getValeurClePrimaire(): int
    {
        return $this->getIdQuestion();
    }

    //Getters
    
    public function getSections(): ?array
    {
        if($this->sections == null) {
            $this->sections = (new SectionRepository)->selectAllByQuestion($this->getIdQuestion());
        }
        return $this->sections;
    }

    public function getRedacteurs(): ?array
    {
        if($this->redacteurs == null) {
            $this->redacteurs = (new RedacteurRepository)->selectAllByQuestion($this->getIdQuestion());
        }
        return $this->redacteurs;
    }

    public function getVotants(): ?array
    {
        if($this->votants == null) {
            $this->votants = (new VotantRepository)->selectAllByQuestion($this->getIdQuestion());
        }
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
        if($this->dateDebutRedaction == null || $this->dateFinRedaction == null || $this->dateOuvertureVotes == null || $this->dateFermetureVotes == null) {
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

    public function setSections(array $sections): void
    {
        $this->sections = $sections;
    }

    public function setRedacteurs(array $redacteurs): void
    {
        $this->redacteurs = $redacteurs;
    }

    public function setVotants(array $votants): void
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

    public function setSystemeVote(AbstractSystemeVote $systemeVote): void
    {
        $this->systemeVote = $systemeVote;
    }

    //Caster

    public static function castIfNotNull($object, $errorUrl = "frontController.php", $errorMessage = "[OBJECT] n'existe pas"): Question
    {
        return static::castToClassIfNotNull($object, Question::class, $errorUrl, $errorMessage);
    }

    //Serialisation

    public function jsonSerialize() : mixed
    {
        return [
            'id_question' => $this->getIdQuestion(),
            'titre_question' => $this->getTitre(),
            'description_question' => $this->getDescription(),
            'organisateur' => $this->getOrganisateur(),
            'sections' => $this->getSections(),
            'redacteurs' => $this->getRedacteurs(),
            'votants' => $this->getVotants(),
            'date_debut_redaction' => $this->getDateDebutRedaction(),
            'date_fin_redaction' => $this->getDateFinRedaction(),
            'date_ouverture_votes' => $this->getDateOuvertureVotes(),
            'date_fermeture_votes' => $this->getDateFermetureVotes(),
            'systeme_vote' => $this->getSystemeVote()
        ];
    }
}
