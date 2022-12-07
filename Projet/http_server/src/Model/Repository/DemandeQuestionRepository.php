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
        return 'id_demande_question';
    }

    protected function getNomsColonnes(): array
    {
        return [
            'titre_demande_question',
            'description_demande_question',
            'id_organisateur'
        ];
    }

    public function construire(array $row): DemandeQuestion
    {
        $question = new DemandeQuestion(
            $row['id_demande_question'],
            $row['titre_demande_question'],
            $row['description_demande_question'],
            (new UtilisateurRepository)->select($row['id_organisateur'])
        );
        return $question;
    } 
}
