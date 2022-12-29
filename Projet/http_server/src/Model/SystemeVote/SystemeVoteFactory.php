<?php

namespace App\SAE\Model\SystemeVote;

use App\SAE\Model\DataObject\Question;

class SystemeVoteFactory
{
    
        public static function createSystemeVote(?string $type, Question $question): AbstractSystemeVote
        {
            switch ($type) {
                case "majoritaire_a_un_tour":
                    return new UninominalMajoritaireAUnTour($question);
                case "approbation":
                    return new VoteParApprobation($question);
                case "jugement_majoritaire":
                    return new JugementMajoritaire($question);
                case "alternatif":
                    return new VoteAlternatif($question);
                default:
                    return new UninominalMajoritaireAUnTour($question);
            }
        }



}
