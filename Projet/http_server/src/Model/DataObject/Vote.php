<?php

namespace App\SAE\Model\DataObject;

class Vote extends AbstractDataObject
{

    private Proposition $proposition;
    private Utilisateur $votant;
    private ?int $valeur;

    public function __construct(Proposition $proposition, Utilisateur $votant, ?int $valeur)
    {
        $this->proposition = $proposition;
        $this->votant = $votant;
        $this->valeur = $valeur;
    }
    
    public function formatTableau(): array {
        return [
            "id_proposition" => $this->proposition->getIdProposition(),
            "id_votant" => $this->votant->getIdUtilisateur(),
            'valeur' => $this->valeur
        ];
    }

    public function getValeurClePrimaire() {
        $idVotant = $this->votant->getIdUtilisateur();
        $idProposition = $this->proposition->getIdProposition();
        return $idVotant . ", " . $idProposition;
    }

    //getters
    public function getProposition(): Proposition
    {
        return $this->proposition;
    }

    public function getVotant(): Utilisateur
    {
        return $this->votant;
    }

    public function getValeur(): ?int
    {
        return $this->valeur;
    }
}
