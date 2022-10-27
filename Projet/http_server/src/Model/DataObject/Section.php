<?php

namespace App\SAE\Model\DataObject;

class Section extends AbstractDataObject {

    private int $idSection;
    private int $idQuestion;
    private string $nomSection;

    public function __construct(int $idSection, int $idQuestion, string $nomSection) {
        $this->idSection = $idSection;
        $this->idQuestion = $idQuestion;
        $this->nomSection = $nomSection;
    }

    public function formatTableau(): array {
        return [
            'idquestion' => $this->idQuestion,
            'nomsection' => $this->nomSection
        ];
    }

    public function getValeurClePrimaire(): int {
        return $this->getIdSection();
    }

    // Getters

    public function getIdSection(): int {
        return $this->idSection;
    }

}