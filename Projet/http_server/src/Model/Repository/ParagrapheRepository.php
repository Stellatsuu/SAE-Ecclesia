<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\Paragraphe;

class ParagrapheRepository extends AbstractRepository
{

    protected function getNomTable(): string
    {
        return "paragraphe";
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
            $objetFormatTableau['id_section'],
            $objetFormatTableau['contenu_paragraphe']
        );
    }

    public function selectAllByProposition(int $idProposition): array
    {
        $sql = <<<SQL
        SELECT *
            FROM paragraphe
            WHERE id_proposition = :idProposition
            ORDER BY id_section
        SQL;

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute(["idProposition" => $idProposition]);
        $statement = $pdo->fetchAll();

        $paragraphes = [];

        foreach ($statement as $paragraphe) {
            $paragraphes[] = $this->construire($paragraphe);
        }

        return $paragraphes;
    }

    public function selectByPropositionEtSection(int $idProposition, int $idSection): Paragraphe
    {

        $sql = <<<SQL
        SELECT *
            FROM paragraphe
            WHERE id_proposition = :idProposition
            AND id_section = :idSection
        SQL;

        $statement = DatabaseConnection::getPdo()->prepare($sql);
        $statement->execute([
            "idProposition" => $idProposition,
            "idSection" => $idSection
        ]);

        return $this->construire($statement->fetch());
    }
}
