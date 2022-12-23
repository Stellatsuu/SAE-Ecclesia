<?php

namespace App\SAE\Controller;

use App\SAE\Lib\ConnexionUtilisateur;
use App\SAE\Lib\PhaseQuestion;
use App\SAE\Model\DataObject\Proposition;
use App\SAE\Model\DataObject\Question;
use App\SAE\Model\DataObject\Vote;
use App\SAE\Model\HTTP\Session;
use App\SAE\Model\Repository\PropositionRepository;
use App\SAE\Model\Repository\QuestionRepository;
use App\SAE\Model\Repository\VoteRepository;
use App\SAE\Model\Repository\UtilisateurRepository;
use App\SAE\Model\Repository\VotantRepository;

class VoteController extends MainController
{

    public static function voter() {
        $idQuestion = static::getIfSetAndNumeric("idQuestion");

        $question = Question::castIfNotNull((new QuestionRepository())->select($idQuestion));

        $systemeVote = $question->getSystemeVote();

        $systemeVote->traiterVote();
    }

}
