<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\DemandeQuestion;

class DemandeQuestionRepository extends AbstractRepository
{

    protected function getNomTable(): string
    {
        return 'demande_question';
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
            'idutilisateur'
        ];
    }

    public function construire(array $row): DemandeQuestion
    {
        $question = new DemandeQuestion(
            $row['idquestion'],
            $row['titre'],
            $row['intitule'],
            (new UtilisateurRepository)->select($row['idutilisateur'])
        );
        return $question;
    }
}
