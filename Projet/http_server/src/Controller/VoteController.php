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

        $session = Session::getInstance();

        if(!$session->contient("idUtilisateur")) {
            static::error("frontController.php", "Vous devez être connecté pour voter");
        }

        if(!isset($_POST['idProposition']) || !is_numeric($_POST['idProposition'])) {
            static::error("frontController.php", "Aucune proposition n'a été sélectionnée");
            return;
        }
        $idProposition = intval($_POST['idProposition']);

        $proposition = Proposition::castIfNotNull((new PropositionRepository)->select($idProposition));
        if(!$proposition) {
            static::error("frontController.php", "La proposition n'existe pas");
            return;
        }

        $question = $proposition->getQuestion();
        $phase = $question->getPhase();
        if($phase !== PhaseQuestion::Vote) {
            static::error("frontController.php", "La question n'est pas en phase de vote");
            return;
        }

        $idUtilisateur = $session->lire("idUtilisateur");

        $estVotant = (new VotantRepository)->existeVotant($proposition->getQuestion()->getIdQuestion(), $idUtilisateur);
        if(!$estVotant) {
            static::error("frontController.php?controller=proposition&action=afficherPropositions&idQuestion={$question->getIdQuestion()}&idUtilisateur={$idUtilisateur}", "Vous n'êtes pas votant pour cette question");
            return;
        }

        $aDejaVote = (new VoteRepository)->existeVoteSurQuestion($proposition->getQuestion()->getIdQuestion(), $idUtilisateur);
        if($aDejaVote) {
            static::error("frontController.php?controller=proposition&action=afficherPropositions&idQuestion={$question->getIdQuestion()}&idUtilisateur={$idUtilisateur}", "Vous avez déjà voté sur cette question");
            return;
        }

        $votant = (new UtilisateurRepository())->select($idUtilisateur);
        $vote = new Vote($proposition, $votant, 1);
        (new VoteRepository)->insert($vote);

    static::message("frontController.php?controller=proposition&action=afficherPropositions&idQuestion={$question->getIdQuestion()}&idUtilisateur={$idUtilisateur}", "Votre vote a bien été pris en compte");
    }
}
