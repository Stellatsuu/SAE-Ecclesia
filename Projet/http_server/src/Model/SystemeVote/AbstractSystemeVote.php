<?php

namespace App\SAE\Model\SystemeVote;

use App\SAE\Model\DataObject\Question;

abstract class AbstractSystemeVote
{

    private Question $question;

    public function getResultats() {
        return [];
    }


    //public abstract function getResultats(): array;

    /**
     * @return string
     * Renvoie le nom du système de vote, à utiliser dans la BDD
     */
    public abstract function getNom(): string;

    /**
     * @return string
     * Renvoie le nom complet du système de vote, à utiliser dans l'interface
     */
    public abstract function getNomComplet(): string;

    /**
     * @return string
     * Renvoie le code HTML à afficher pour afficher l'interface de vote
     */
    public abstract function afficherInterfaceVote(): string;

    /**
     * @return string
     * Renvoie le code HTML à afficher pour afficher les résultats
     */
    public abstract function afficherResultats(): string;

    /**
     * Agit comme l'action du contrôleur "voter", spécifique à chaque système de vote
     */
    public abstract function traiterVote(): void;


    public function __construct(Question $question)
    {
        $this->question = $question;
    }

    public function getQuestion(): Question
    {
        return $this->question;
    }
}
