<?php

namespace App\SAE\Model\SystemeVote;

use App\SAE\Model\DataObject\Question;

class SystemeVoteFactory
{
    
        public static function createSystemeVote(?string $type, Question $question): AbstractSystemeVote
        {
            switch ($type) {
                case "majoritaire_a_un_tour":
                    return new MajoritaireAUnTour($question);
                default:
                    return new MajoritaireAUnTour($question);
            }
        }



}
