<?php

namespace App\SAE\Model\DataObject;

use App\SAE\Model\Repository\PropositionRepository;
use App\SAE\Model\Repository\UtilisateurRepository;

class Vote extends AbstractDataObject
{
    private int $idProposition;
    private int $idVotant;

    private ?Proposition $proposition;
    private ?Utilisateur $votant;
    
    private ?int $valeur;

    public function __construct(Proposition|int $proposition, Utilisateur|int $votant, ?int $valeur)
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
            $this->idVotant = $votant->getIdUtilisateur();
        } else {
            $this->votant = null;
            $this->idVotant = $votant;
        }
    }
    
    public function formatTableau(): array {
        return [
            "id_proposition" => $this->idProposition,
            "id_votant" => $this->idVotant,
            'valeur' => $this->valeur
        ];
    }

    public function getValeurClePrimaire() {
        return $this->idVotant . ", " . $this->idProposition;
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
        if($this->votant == null) {
            $this->votant = (new UtilisateurRepository())->select($this->idVotant);
        }
        return $this->votant;
    }

    public function getIdProposition(): int
    {
        return $this->idProposition;
    }

    public function getIdVotant(): int
    {
        return $this->idVotant;
    }

    public function getValeur(): ?int
    {
        return $this->valeur;
    }
}
