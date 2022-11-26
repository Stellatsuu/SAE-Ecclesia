<?php

namespace App\SAE\Model\SystemeVote;

use App\SAE\Model\DataObject\Question;

class MajoritaireAUnTour extends AbstractSystemeVote
{

    private $votes;
    private $question;

    public function getNom(): string
    {
        return "majoritaire_a_un_tour";
    }
    
    public function getResultats(): array
    {
        return [];
    }
}
