<?php

namespace App\SAE\Controller;

use App\SAE\Lib\ConnexionUtilisateur;
use App\SAE\Model\DataObject\Question;
use App\SAE\Model\Repository\QuestionRepository;
use App\SAE\Model\Repository\VoteRepository;

class VoteController extends MainController
{

    public static function voter()
    {
        $idQuestion = static::getIfSetAndNumeric("idQuestion");

        $question = Question::castIfNotNull((new QuestionRepository())->select($idQuestion));

        $systemeVote = $question->getSystemeVote();

        $systemeVote->traiterVote();
    }

    public static function supprimerVote(){
        $username = ConnexionUtilisateur::getUsernameSiConnecte();
        $idQuestion = static::getIfSetAndNumeric("idQuestion");
        $lienAfficherPropositions = "frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestion";

        (new VoteRepository())->deleteAllByQuestionEtVotant($idQuestion, $username);

        VoteController::message($lienAfficherPropositions, "Votre vote a été supprimé");
    }

}
