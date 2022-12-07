<?php

namespace App\SAE\Model\Repository;

use App\SAE\Config\Conf;
use App\SAE\Model\DataObject\AbstractDataObject;
use App\SAE\Model\DataObject\Proposition;
use App\SAE\Model\DataObject\Question;
use App\SAE\Model\DataObject\Utilisateur;

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
            "id_redacteur",
            "id_question"
        ];
    }

    protected function construire(array $objetFormatTableau): Proposition
    {
        return new Proposition(
            $objetFormatTableau['id_proposition'],
            $objetFormatTableau['titre_proposition'],
            $objetFormatTableau['id_redacteur'],
            $objetFormatTableau['id_question'],
            (new ParagrapheRepository())->selectAllByProposition($objetFormatTableau['id_proposition'])
        );
    }

    public function selectByQuestionEtRedacteur(int $idQuestion, int $idRedacteur): ?Proposition
    {
        $sql = <<<SQL
        SELECT * 
            FROM proposition 
            WHERE id_redacteur = :id_redacteur 
            AND id_question = :id_question
        SQL;

        $values = [
            "id_redacteur" => $idRedacteur,
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
        $proposition = $this->selectByQuestionEtRedacteur($object->getIdQuestion(), $object->getIdResponsable());

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
