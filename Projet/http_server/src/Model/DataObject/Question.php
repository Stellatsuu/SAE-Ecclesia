<?php

namespace App\SAE\Model\DataObject;

class Question
{

    private int $idQuestion;

    private string $question;

    private string $intitule;

    private bool $estValide;

    private Utilisateur $organisateur;

    public function __construct(int $idQuestion, string $question, string $intitule, bool $estValide, Utilisateur $organisateur)
    {
        $this->idQuestion = $idQuestion;
        $this->question = $question;
        $this->intitule = $intitule;
        $this->estValide = $estValide;
        $this->organisateur = $organisateur;
    }

    // Getters
    
    public function getIdQuestion(): int
    {
        return $this->idQuestion;
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function getIntitule(): string
    {
        return $this->intitule;
    }

    public function getEstValide(): bool
    {
        return $this->estValide;
    }

    public function getOrganisateur(): Utilisateur
    {
        return $this->organisateur;
    }
    
}
