<?php

namespace App\SAE\Controller;

use App\SAE\Model\DataObject\Question;
use App\SAE\Model\Repository\QuestionRepository;

class VoteController extends MainController
{

    public static function voter()
    {
        $idQuestion = static::getIfSetAndNumeric("idQuestion");

        $question = Question::castIfNotNull((new QuestionRepository())->select($idQuestion));

        $systemeVote = $question->getSystemeVote();

        $systemeVote->traiterVote();
    }

}
