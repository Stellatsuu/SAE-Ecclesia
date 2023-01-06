<?php

namespace App\SAE\Model\DataObject;

use App\SAE\Model\Repository\PropositionRepository;
use App\SAE\Model\Repository\UtilisateurRepository;

class Vote extends AbstractDataObject
{
    private int $idProposition;
    private string $usernameVotant;

    private ?Proposition $proposition;
    private ?Utilisateur $votant;
    
    private ?int $valeur;

    public function __construct(Proposition|int $proposition, Utilisateur|string $votant, ?int $valeur)
    {
        $this->valeur = $valeur;

        if ($proposition instanceof Proposition) {
            $this->proposition = $proposition;
            $this->idProposition = $proposition->getIdProposition();
        } else {
            $this->proposition = null;
            $this->idProposition = $proposition;
        }

        if ($votant instanceof Utilisateur) {
            $this->votant = $votant;
            $this->usernameVotant = $votant->getUsername();
        } else {
            $this->votant = null;
            $this->usernameVotant = $votant;
        }
    }
    
    public function formatTableau(): array
    {
        return [
            "id_proposition" => $this->idProposition,
            "username_votant" => $this->usernameVotant,
            'valeur' => $this->valeur
        ];
    }

    public function getValeurClePrimaire() : string
    {
        return $this->usernameVotant . ", " . $this->idProposition;
    }

    //getters
    public function getProposition(): Proposition
    {
        if($this->proposition == null) {
            $this->proposition = (new PropositionRepository())->select($this->idProposition);
        }
        return $this->proposition;
    }

    public function getVotant(): Utilisateur
    {
        if ($this->votant == null) {
            $this->votant = (new UtilisateurRepository())->select($this->usernameVotant);
        }
        return $this->votant;
    }

    public function getIdProposition(): int
    {
        return $this->idProposition;
    }

    public function getUsernameVotant(): string
    {
        return $this->usernameVotant;
    }

    public function getValeur(): ?int
    {
        return $this->valeur;
    }

    //Caster

    public static function castIfNotNull($object, $errorUrl = "frontController.php", $errorMessage = "[OBJECT] n'existe pas"): Vote
    {
        return static::castToClassIfNotNull($object, Vote::class, $errorUrl, $errorMessage);
    }
}
