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
        return "Proposition";
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
            (new UtilisateurRepository())->select($objetFormatTableau['id_redacteur']),
            (new QuestionRepository())->select($objetFormatTableau['id_question']),
            (new ParagrapheRepository())->selectAllByProposition($objetFormatTableau['id_proposition'])
        );
    }

    public function selectByQuestionEtRedacteur(Question $question, int $idRedacteur): ?Proposition
    {
        $sql = "SELECT * FROM {$this->getNomTable()} WHERE id_redacteur = :id_redacteur AND id_question = :id_question";
        $values = [
            "id_redacteur" => $idRedacteur,
            "id_question" => $question->getIdQuestion()
        ];

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute($values);

        return static::construire($pdo->fetch());
    }

    public function deleteCoAuteurs($idProposition)
    {
        $sql = "CALL supprimer_co_auteurs(:id_proposition)";
        $values = [
            "id_proposition" => $idProposition
        ];

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute($values);
    }

    public function addCoAuteur($idParagraphe, $idUtilisateur)
    {
        $sql = "INSERT INTO co_auteur (id_paragraphe, id_utilisateur) VALUES (:id_paragraphe, :id_utilisateur)";
        $values = [
            "id_paragraphe" => $idParagraphe,
            "id_utilisateur" => $idUtilisateur
        ];

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute($values);
    }

    public function addCoAuteurGlobal($idProposition, $idUtilisateur)
    {
        $sql = "INSERT INTO co_auteur (id_paragraphe, id_utilisateur) SELECT p.id_paragraphe, :id_utilisateur FROM paragraphe p WHERE p.id_proposition = :id_proposition";
        $values = [
            "id_proposition" => $idProposition,
            "id_utilisateur" => $idUtilisateur
        ];

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute($values);
    }

    public static function selectCoAuteurs($idProposition)
    {
        $sql = "SELECT 
                    DISTINCT id_utilisateur 
                FROM co_auteur ca 
                    JOIN paragraphe p 
                    ON p.id_paragraphe = ca.id_paragraphe 
                WHERE p.id_proposition = :id_proposition";
        $values = [
            "id_proposition" => $idProposition
        ];

        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute($values);

        $resultat = [];
        foreach($pdo as $row) {
            $resultat[] = (new UtilisateurRepository)->select($row['id_utilisateur']);
        }
        return $resultat;
    }
}
