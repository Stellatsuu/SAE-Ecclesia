<?php

namespace App\SAE\Model\Repository;

use App\SAE\Model\DataObject\Question;

class QuestionRepository extends AbstractRepository
{

    protected function getNomTable(): string
    {
        return 'question';
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
            'idutilisateur',
            'datedebutredaction',
            'datefinredaction',
            'dateouverturevotes',
            'datefinvotes'
        ];
    }

    public function construire(array $row): Question
    {
        $question = new Question(
            $row['idquestion'],
            $row['titre'],
            $row['intitule'],
            (new UtilisateurRepository)->select($row['idutilisateur']),
            (new SectionRepository)->selectAllByQuestion($row['idquestion']),
            $row['datedebutredaction'],
            $row['datefinredaction'],
            $row['dateouverturevotes'],
            $row['datefinvotes']
        );
        return $question;
    }
}
