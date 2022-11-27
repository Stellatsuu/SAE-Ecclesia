<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\AbstractDataObject;
use App\SAE\Model\DataObject\Paragraphe;
use App\SAE\Model\DataObject\Utilisateur;

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
            $objetFormatTableau['id_section'],
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
            $paragraphes[] = $this->construire($paragraphe);
        }

        return $paragraphes;
    }

    public function getCoAuteurs(int $idParagraphe): array{
        $sql = "SELECT * FROM co_auteur WHERE id_paragraphe = :idParagraphe;";
        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute(["idParagraphe" => $idParagraphe]);

        $coAuteurs = [];
        foreach($pdo->fetchAll() as $auteur){
            $coAuteurs[] = Utilisateur::toUtilisateur((new UtilisateurRepository())->select($auteur['id_utilisateur']));
        }

        return $coAuteurs;
    }

    public function estCoAuteur(int $idParagraphe, int $idUtilisateur): bool{
        $sql = "SELECT COUNT(*) AS est_coauteur FROM co_auteur WHERE id_paragraphe = :idParagraphe AND id_utilisateur = :idUtilisateur";
        $pdo = DatabaseConnection::getPdo()->prepare($sql);
        $pdo->execute([
            "idParagraphe" => $idParagraphe,
            "idUtilisateur" => $idUtilisateur
        ]);

        return $pdo->fetch()['est_coauteur'] > 0;
    }

    public function selectByPropositionEtSection(int $idProposition, int $idSection): Paragraphe{
        $sql = "SELECT * FROM {$this->getNomTable()} WHERE id_proposition = :idProposition AND id_section = :idSection";
        $statement = DatabaseConnection::getPdo()->prepare($sql);
        $statement->execute([
            "idProposition" => $idProposition,
            "idSection" => $idSection
        ]);

        return $this->construire($statement->fetch());
    }
}