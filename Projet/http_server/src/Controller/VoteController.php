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
    public static function voter() {

        $session = static::getSessionSiConnecte();

        $idProposition = static::getIfSetAndNumeric("idProposition");

        $proposition = Proposition::castIfNotNull((new PropositionRepository)->select($idProposition));

        $question = $proposition->getQuestion();
        $phase = $question->getPhase();
        if($phase !== PhaseQuestion::Vote) {
            static::error("frontController.php", "La question n'est pas en phase de vote");
            return;
        }

        $idUtilisateur = $session->lire("idUtilisateur");
        $idQuestion = $question->getIdQuestion();

        /**
         * @var string URL de afficherPropositions
         */
        $AP_URL = "frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestion";

        $estVotant = (new VotantRepository)->existsForQuestion($question->getIdQuestion(), $idUtilisateur);
        if(!$estVotant) {
            static::error($AP_URL, "Vous n'êtes pas votant pour cette question");
        }

        $aDejaVote = (new VoteRepository)->existsForQuestion($proposition->getQuestion()->getIdQuestion(), $idUtilisateur);
        if($aDejaVote) {
            static::error($AP_URL, "Vous avez déjà voté sur cette question");
        }

        $votant = (new UtilisateurRepository())->select($idUtilisateur);
        $vote = new Vote($proposition, $votant, 1);
        (new VoteRepository)->insert($vote);

    static::message($AP_URL, "Votre vote a bien été pris en compte");
    }
}
