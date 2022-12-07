<?php

namespace App\SAE\Model\DataObject;

use App\SAE\Model\Repository\QuestionRepository;
use JsonSerializable;

class Section extends AbstractDataObject implements JsonSerializable {

    private int $idSection;
    private int $idQuestion;

    private ?Question $question;

    private string $nomSection;
    private string $descriptionSection;

    public function __construct(int $idSection, Question|int $question, string $nomSection, string $descriptionSection) {
        $this->idSection = $idSection;
        $this->nomSection = $nomSection;
        $this->descriptionSection = $descriptionSection;

        if ($question instanceof Question) {
            $this->question = $question;
            $this->idQuestion = $question->getIdQuestion();
        } else {
            $this->question = null;
            $this->idQuestion = $question;
        }
    }

    //Respect du contrat

    public function formatTableau(): array {
        return [
            'id_question' => $this->idQuestion,
            'nom_section' => $this->nomSection,
            'description_section' => $this->descriptionSection
        ];
    }

    public function getValeurClePrimaire(): int {
        return $this->getIdSection();
    }

    // Getters

    public function getIdSection(): int {
        return $this->idSection;
    }

    public function getIdQuestion(): int {
        return $this->idQuestion;
    }

    public function getQuestion(): ?Question {
        if ($this->question === null) {
            $this->question = (new QuestionRepository)->select($this->idQuestion);
        }
        return $this->question;
    }

    public function getNomSection(): string {
        return $this->nomSection;
    }

    public function getDescriptionSection(): string {
        return $this->descriptionSection;
    }

    // Setters

    public function setIdQuestion(int $idQuestion): void {
        $this->idQuestion = $idQuestion;
    }

    //Serialisation

    public function JsonSerialize(): array {
        return [
            'id_section' => $this->idSection,
            'id_question' => $this->idQuestion,
            'nom_section' => $this->nomSection,
            'description_section' => $this->descriptionSection
        ];
    }

    //Caster

    public static function castIfNotNull($object, $errorUrl = "frontController.php", $errorMessage = "[OBJECT] n'existe pas"): Section
    {
        return static::castToClassIfNotNull($object, Section::class, $errorUrl, $errorMessage);
    }
}