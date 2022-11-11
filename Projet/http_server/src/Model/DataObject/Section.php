<?php

namespace App\SAE\Model\DataObject;

use JsonSerializable;

class Section extends AbstractDataObject implements JsonSerializable {

    private int $idSection;
    private int $idQuestion;
    private string $nomSection;
    private string $descriptionSection;

    public function __construct(int $idSection, int $idQuestion, string $nomSection, string $descriptionSection) {
        $this->idSection = $idSection;
        $this->idQuestion = $idQuestion;
        $this->nomSection = $nomSection;
        $this->descriptionSection = $descriptionSection;
    }

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

    public function JsonSerialize(): array {
        return [
            'id_section' => $this->idSection,
            'id_question' => $this->idQuestion,
            'nom_section' => $this->nomSection,
            'description_section' => $this->descriptionSection
        ];
    }
}