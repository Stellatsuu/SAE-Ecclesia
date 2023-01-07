<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\AbstractDataObject;
use App\SAE\Model\DataObject\Proposition;

class PropositionRepository extends AbstractRepository
{

    protected function getNomTable(): string
    {
        return "proposition";
    }

    protected function getNomClePrimaire(): string
    {
        return "id_proposition";
    }

    protected function getNomsColonnes(): array
    {
        return [
            "titre_proposition",
            "username_responsable",
            "id_question"
        ];
    }

    protected function construire(array $objetFormatTableau): Proposition
    {
        return new Proposition(
            $objetFormatTableau['id_proposition'],
            $objetFormatTableau['titre_proposition'],
            $objetFormatTableau['username_responsable'],
            $objetFormatTableau['id_question']
        );
    }

    public function selectByQuestionEtResponsable(int $idQuestion, string $username): ?Proposition
    {
        $sql = <<<SQL
        SELECT * 
            FROM proposition 
            WHERE username_responsable = :username
            AND id_question = :id_question
        SQL;

        $values = [
            "username" => $username,
            "id_question" => $idQuestion
        ];

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute($values);

        $ligne = $pdo->fetch();
        if ($ligne === false) {
            return null;
        }
        return $this->construire($ligne);
    }

    public function insert(AbstractDataObject $object): void
    {
        parent::insert($object);

        $object = Proposition::castIfNotNull($object);
        $proposition = $this->selectByQuestionEtResponsable($object->getIdQuestion(), $object->getUsernameResponsable());

        foreach ($object->getParagraphes() as $paragraphe) {
            $paragraphe->setIdProposition($proposition->getIdProposition());
            (new ParagrapheRepository())->insert($paragraphe);
        }
    }

    public function selectAllByQuestion(int $idQuestion): array
    {
        $sql = <<<SQL
        SELECT * 
            FROM proposition 
            WHERE id_question = :id_question
        SQL;

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
}
