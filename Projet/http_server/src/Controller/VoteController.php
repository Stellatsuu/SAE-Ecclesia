<?php

namespace App\SAE\Controller;

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
    public static function voter()
    {

        $session = static::getSessionSiConnecte();
        $username = $session->lire("username");

        $idProposition = static::getIfSetAndNumeric("idProposition");
        $proposition = Proposition::castIfNotNull((new PropositionRepository)->select($idProposition));

        $question = $proposition->getQuestion();
        $idQuestion = $question->getIdQuestion();
        $phase = $question->getPhase();
        if ($phase !== PhaseQuestion::Vote) {
            static::error("frontController.php", "La question n'est pas en phase de vote");
            return;
        }

        /**
         * @var string URL de afficherPropositions
         */
        $AP_URL = "frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestion";

        $estVotant = (new VotantRepository)->existsForQuestion($question->getIdQuestion(), $username);
        if (!$estVotant) {
            static::error(LMQ_URL, "Vous n'êtes pas votant pour cette question");
        }

        $aDejaVote = (new VoteRepository)->existsForQuestion($proposition->getIdQuestion(), $username);
        if ($aDejaVote) {
            static::error($AP_URL, "Vous avez déjà voté sur cette question");
        }

        $vote = new Vote($proposition, $username, 1);
        (new VoteRepository)->insert($vote);

        static::message($AP_URL, "Votre vote a bien été pris en compte");
    }
}
