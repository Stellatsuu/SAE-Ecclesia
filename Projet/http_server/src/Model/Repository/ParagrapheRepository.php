<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\AbstractDataObject;
use App\SAE\Model\DataObject\Paragraphe;

class ParagrapheRepository extends AbstractRepository{

    protected function getNomTable(): string
    {
        return "Paragraphe";
    }

    protected function getNomClePrimaire(): string
    {
        return "id_paragraphe";
    }

    protected function getNomsColonnes(): array
    {
        return [
            "id_proposition",
            "id_section",
            "contenu_paragraphe"
        ];
    }

    protected function construire(array $objetFormatTableau): Paragraphe
    {
        return new Paragraphe(
            $objetFormatTableau['id_paragraphe'],
            $objetFormatTableau['id_proposition'],
            (new SectionRepository())->select($objetFormatTableau['id_section']),
            $objetFormatTableau['contenu_paragraphe']
        );
    }

    public function selectAllByProposition(int $idProposition): array{
        $sql = "SELECT * FROM {$this->getNomTable()} WHERE id_proposition = :idProposition";
        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute(["idProposition" => $idProposition]);
        $statement = $pdo->fetchAll();

        $paragraphes = [];

        foreach($statement as $paragraphe){
            $paragraphe[] = $this->construire($paragraphe);
        }

        return $paragraphes;
    }
}