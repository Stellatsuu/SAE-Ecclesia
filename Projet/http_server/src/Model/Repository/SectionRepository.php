<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\Section;


class SectionRepository extends AbstractRepository
{
    protected function getNomTable(): string
    {
        return 'section';
    }
    protected function getNomClePrimaire(): string
    {
        return 'id_section';
    }
    protected function getNomsColonnes(): array
    {
        return [
            'id_question',
            'nom_section',
            'description_section'
        ];
    }
    protected function construire(array $objetFormatTableau): Section
    {
        return new Section(
            $objetFormatTableau['id_section'],
            $objetFormatTableau['id_question'],
            $objetFormatTableau['nom_section'],
            $objetFormatTableau['description_section']
        );
    }

    public function selectAllByQuestion(int $idQuestion): array
    {
        $sql = "SELECT * FROM section WHERE id_question = :id_question";
        $pdo = DatabaseConnection::getPdo();
        $values = [
            'id_question' => $idQuestion
        ];

        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->execute($values);

        $resultat = [];
        foreach ($pdoStatement as $ligne) {
            $resultat[] = $this->construire($ligne);
        }
        return $resultat;
    }

    public function deleteAllByQuestion(int $idQuestion): void
    {
        $sql = "DELETE FROM section WHERE id_question = :id_question";
        $pdo = DatabaseConnection::getPdo();
        $values = [
            'id_question' => $idQuestion
        ];

        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->execute($values);
    }
}
