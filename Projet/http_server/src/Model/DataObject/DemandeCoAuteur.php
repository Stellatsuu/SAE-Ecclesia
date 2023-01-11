<?php

namespace App\SAE\Model\DataObject;

use App\SAE\Model\Repository\PropositionRepository;
use App\SAE\Model\Repository\UtilisateurRepository;

class DemandeCoAuteur extends AbstractDataObject
{
    private string $usernameDemandeur;
    private int $idProposition;

    private ?Utilisateur $demandeur;
    private ?Proposition $proposition;

    private string $message;

    public function __construct(Utilisateur|string $demandeur, Proposition|int $proposition, string $message)
    {
        $this->message = $message;

        if ($demandeur instanceof Utilisateur) {
            $this->demandeur = $demandeur;
            $this->usernameDemandeur = $demandeur->getUsername();
        } else {
            $this->demandeur = null;
            $this->usernameDemandeur = $demandeur;
        }

        if ($proposition instanceof Proposition) {
            $this->proposition = $proposition;
            $this->idProposition = $proposition->getIdProposition();
        } else {
            $this->proposition = null;
            $this->idProposition = $proposition;
        }
    }

    //Respect du contrat

    public function formatTableau(): array
    {
        return [
            'username_demandeur' => $this->usernameDemandeur,
            'id_proposition' => $this->idProposition,
            'message' => $this->message
        ];
    }

    public function getValeurClePrimaire(): string
    {
        return $this->usernameDemandeur . ", " . $this->idProposition;
    }

    //getters

    public function getUsernameDemandeur(): string
    {
        return $this->usernameDemandeur;
    }

    public function getIdProposition(): int
    {
        return $this->idProposition;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getDemandeur(): Utilisateur
    {
        if ($this->demandeur == null) {
            $this->demandeur = (new UtilisateurRepository())->select($this->usernameDemandeur);
        }
        return $this->demandeur;
    }

    public function getProposition(): Proposition
    {
        if ($this->proposition == null) {
            $this->proposition = (new PropositionRepository())->select($this->idProposition);
        }
        return $this->proposition;
    }

    //Caster

    public static function castIfNotNull($object, $errorUrl = "frontController.php", $errorMessage = "[OBJECT] n'existe pas") : DemandeCoAuteur
    {
        return static::castToClassIfNotNull($object, DemandeCoAuteur::class, $errorUrl, $errorMessage);
    }
}
