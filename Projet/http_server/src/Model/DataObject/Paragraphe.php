<?php

namespace App\SAE\Model\DataObject;

class Paragraphe extends AbstractDataObject{

    private int $idParagraphe;
    private int $idProposition;
    private Section $section;
    private string $contenuParagraphe;

    /**
     * @param int $idParagraphe
     * @param Proposition $proposition
     * @param Section $section
     * @param string $contenuParagraphe
     */
    public function __construct(int $idParagraphe, int $idProposition, Section $section, string $contenuParagraphe)
    {
        $this->idParagraphe = $idParagraphe;
        $this->idProposition = $idProposition;
        $this->section = $section;
        $this->contenuParagraphe = $contenuParagraphe;
    }


    public function formatTableau(): array
    {
        return [
            "id_proposition" => $this->idProposition,
            "id_section" => $this->section->getIdSection(),
            "contenu_paragraphe" => $this->contenuParagraphe
        ];
    }

    public function getValeurClePrimaire()
    {
        return $this->idParagraphe;
    }

    /**
     * @return int
     */
    public function getIdParagraphe(): int
    {
        return $this->idParagraphe;
    }

    /**
     * @return Proposition
     */
    public function getIdProposition(): int
    {
        return $this->idProposition;
    }

    /**
     * @return Section
     */
    public function getSection(): Section
    {
        return $this->section;
    }

    /**
     * @return string
     */
    public function getContenuParagraphe(): string
    {
        return $this->contenuParagraphe;
    }

    /**
     * @param int $idProposition
     */
    public function setIdProposition(int $idProposition): void
    {
        $this->idProposition = $idProposition;
    }




}