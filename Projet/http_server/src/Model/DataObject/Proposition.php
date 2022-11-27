<?php

namespace App\SAE\Model\DataObject;

use App\SAE\Model\Repository\ParagrapheRepository;
use App\SAE\Model\Repository\PropositionRepository;
use App\SAE\Model\Repository\QuestionRepository;
use App\SAE\Model\Repository\UtilisateurRepository;

class Proposition extends AbstractDataObject
{

    private int $idProposition;

    private int $idResponsable;
    private int $idQuestion;

    private ?Utilisateur $responsable;
    private ?Question $question;

    private string $titreProposition;
    private array $paragraphes;


    public function __construct(int $idProposition, string $titreProposition, Utilisateur|int $responsable, Question|int $question)
    {
        $this->idProposition = $idProposition;
        $this->titreProposition = $titreProposition;
        $this->paragraphes = [];

        if ($responsable instanceof Utilisateur) {
            $this->responsable = $responsable;
            $this->idResponsable = $responsable->getIdUtilisateur();
        } else {
            $this->responsable = null;
            $this->idResponsable = $responsable;
        }

        if ($question instanceof Question) {
            $this->question = $question;
            $this->idQuestion = $question->getIdQuestion();
        } else {
            $this->question = null;
            $this->idQuestion = $question;
        }
    }

    //Respect du contrat

    public function formatTableau(): array
    {
        return [
            "titre_proposition" => $this->titreProposition,
            "id_redacteur" => $this->idResponsable,
            "id_question" => $this->idQuestion
        ];
    }

    public function getValeurClePrimaire()
    {
        return $this->idProposition;
    }

    // Getters

    public function getIdProposition(): int
    {
        return $this->idProposition;
    }

    public function getIdResponsable(): int
    {
        return $this->idResponsable;
    }

    public function getIdQuestion(): int
    {
        return $this->idQuestion;
    }

    public function getResponsable(): ?Utilisateur
    {
        if ($this->responsable == null) {
            $this->responsable = (new UtilisateurRepository)->select($this->idResponsable);
        }
        return $this->responsable;
    }

    public function getQuestion(): ?Question
    {
        if ($this->question == null) {
            $this->question = (new QuestionRepository)->select($this->idQuestion);
        }
        return $this->question;
    }

    public function getTitreProposition(): string
    {
        return $this->titreProposition;
    }

    public function getParagraphes(): array
    {
        if($this->paragraphes == null){
            $this->paragraphes = (new ParagrapheRepository)->selectAllByProposition($this->idProposition);
        }
        return $this->paragraphes;
    }

    public function getCoAuteurs(): array
    {
        return (new PropositionRepository())->selectCoAuteurs($this->idProposition);
    }

    // Setters

    public function setTitreProposition(string $titreProposition): void
    {
        $this->titreProposition = $titreProposition;
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

    //Caster

    public static function toProposition($object): Proposition
    {
        return $object;
    }
}
