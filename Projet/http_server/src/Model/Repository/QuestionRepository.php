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
            'idquestion',
            'question',
            'intitule',
            'estvalide',
            'idutilisateur'
        ];
    }

    public function construire(array $row): Question
    {
        $question = new Question(
            $row['idquestion'],
            $row['question'],
            $row['intitule'],
            $row['estvalide'],
            (new UtilisateurRepository)->select($row['idutilisateur'])
        );
        return $question;
    }
}
