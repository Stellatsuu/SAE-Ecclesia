<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\DemandeQuestion;

class DemandeQuestionRepository extends AbstractRepository
{

    protected function getNomTable(): string
    {
        return 'demandequestion';
    }

    protected function getNomClePrimaire(): string
    {
        return 'idquestion';
    }

    protected function getNomsColonnes(): array
    {
        return [
            'titre',
            'intitule',
            'estvalide',
            'idutilisateur'
        ];
    }

    public function construire(array $row): DemandeQuestion
    {
        $question = new DemandeQuestion(
            $row['idquestion'],
            $row['titre'],
            $row['intitule'],
            $row['estvalide'],
            (new UtilisateurRepository)->select($row['idutilisateur'])
        );
        return $question;
    }
}
