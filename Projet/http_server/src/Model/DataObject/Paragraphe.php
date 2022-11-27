<?php

namespace App\SAE\Model\DataObject;

use App\SAE\Model\Repository\ParagrapheRepository;
use App\SAE\Model\Repository\PropositionRepository;
use App\SAE\Model\Repository\SectionRepository;

class Paragraphe extends AbstractDataObject
{
    private int $idParagraphe;

    private int $idProposition;
    private int $idSection;

    private ?Proposition $proposition;
    private ?Section $section;

    private string $contenuParagraphe;
    private array $coAuteurs;

    public function __construct(int $idParagraphe, Proposition|int $proposition, Section|int $section, string $contenuParagraphe)
    {
        $this->idParagraphe = $idParagraphe;
        $this->contenuParagraphe = $contenuParagraphe;

        if ($section instanceof Section) {
            $this->section = $section;
            $this->idSection = $section->getIdSection();
        } else {
            $this->section = null;
            $this->idSection = $section;
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
            "id_proposition" => $this->idProposition,
            "id_section" => $this->idSection,
            "contenu_paragraphe" => $this->contenuParagraphe
        ];
    }

    public function getValeurClePrimaire()
    {
        return $this->idParagraphe;
    }

    //Getters

    public function getIdParagraphe(): int
    {
        return $this->idParagraphe;
    }

    public function getIdProposition(): int
    {
        return $this->idProposition;
    }

    public function getIdSection(): int
    {
        return $this->idSection;
    }

    public function getProposition(): Proposition
    {
        if ($this->proposition == null) {
            $this->proposition = (new PropositionRepository())->select($this->idProposition);
        }
        return $this->proposition;
    }

    public function getSection(): Section
    {
        if ($this->section == null) {
            $this->section = (new SectionRepository())->select($this->idSection);
        }
        return $this->section;
    }

    public function getContenuParagraphe(): string
    {
        return $this->contenuParagraphe;
    }

    public function getCoAuteurs(): array
    {
        if ($this->coAuteurs == null) {
            $this->coAuteurs = (new ParagrapheRepository())->getCoAuteurs($this->idParagraphe);
        }
        return $this->coAuteurs;
    }

    //Setters

    public function setIdProposition(int $idProposition): void
    {
        $this->idProposition = $idProposition;
    }

    public function setContenuParagraphe(string $contenuParagraphe): void
    {
        $this->contenuParagraphe = $contenuParagraphe;
    }
}
