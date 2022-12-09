<?php

namespace App\SAE\Model\SystemeVote;

use App\SAE\Model\DataObject\Question;
use App\SAE\Model\Repository\PropositionRepository;
use App\SAE\Model\Repository\VoteRepository;

class MajoritaireAUnTour extends AbstractSystemeVote
{
    public function getNom(): string
    {
        return "majoritaire_a_un_tour";
    }

    public function getResultats(): array
    {
        $resultats = [];

        $propositions = (new PropositionRepository())->selectAllByQuestion($this->getQuestion()->getIdQuestion());
        $votes = (new VoteRepository())->selectAllByQuestion($this->getQuestion()->getIdQuestion());

        foreach ($propositions as $proposition) {
            $resultats[$proposition->getIdProposition()] = 0;
        }

        foreach ($votes as $vote) {
            $idProposition = $vote->getIdProposition();
            $resultats[$idProposition] += 1;
        }

        return $resultats;
    }
}
