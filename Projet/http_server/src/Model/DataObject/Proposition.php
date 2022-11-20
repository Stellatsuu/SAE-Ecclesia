<?php

namespace App\SAE\Model\DataObject;

class Proposition extends AbstractDataObject{

    private int $idProposition;
    private string $titreProposition;
    private Utilisateur $redacteur;
    private Question $question;
    private array $paragraphes;

    /**
     * @param int $idProposition
     * @param string $titreProposition
     * @param Utilisateur $redacteur
     * @param Question $question
     */
    public function __construct(int $idProposition, string $titreProposition, Utilisateur $redacteur, Question $question, array $paragraphes)
    {
        $this->idProposition = $idProposition;
        $this->titreProposition = $titreProposition;
        $this->redacteur = $redacteur;
        $this->question = $question;
        $this->paragraphes = $paragraphes;
    }

    public function formatTableau(): array
    {
        return [
            "titre_proposition" => $this->titreProposition,
            "id_redacteur" => $this->redacteur->getIdUtilisateur(),
            "id_question" => $this->question->getIdQuestion()
        ];
    }

    public function getValeurClePrimaire()
    {
        return $this->idProposition;
    }

    /**
     * @return int
     */
    public function getIdProposition(): int
    {
        return $this->idProposition;
    }

    /**
     * @return string
     */
    public function getTitreProposition(): string
    {
        return $this->titreProposition;
    }

    /**
     * @return Utilisateur
     */
    public function getRedacteur(): Utilisateur
    {
        return $this->redacteur;
    }

    /**
     * @return Question
     */
    public function getQuestion(): Question
    {
        return $this->question;
    }

    /**
     * @return array
     */
    public function getParagraphes(): array
    {
        return $this->paragraphes;
    }

    /**
     * @param array $paragraphes
     */
    public function setParagraphes(array $paragraphes): void
    {
        $this->paragraphes = $paragraphes;
    }

    /**
     * @param int $idProposition
     */
    public function setIdProposition(int $idProposition): void
    {
        $this->idProposition = $idProposition;
    }



}