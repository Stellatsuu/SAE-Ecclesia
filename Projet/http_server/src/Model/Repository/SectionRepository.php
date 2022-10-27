<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\Section;


class SectionRepository extends AbstractRepository
{

    protected function getNomTable(): string {
        return 'section';
    }
    protected function getNomClePrimaire(): string {
        return 'idsection';
    }
    protected function getNomsColonnes(): array {
        return [
            'idquestion',
            'nomsection'
        ];
    }
    protected function construire(array $objetFormatTableau): Section {
        return new Section(
            $objetFormatTableau['idsection'],
            $objetFormatTableau['idquestion'],
            $objetFormatTableau['nomsection']
        );
    }

    public function selectAllByQuestion(int $idQuestion): array {
        $sql = "SELECT * FROM section WHERE idquestion = :idquestion";
        $pdo = DatabaseConnection::getPdo();
        $values = [
            'idquestion' => $idQuestion
        ];

        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->execute($values);

        $resultat = [];
        foreach ($pdoStatement as $ligne) {
            $resultat[] = $this->construire($ligne);
        }
        return $resultat;
    }
}
