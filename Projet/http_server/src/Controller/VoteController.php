<?php

namespace App\SAE\Controller;

use App\SAE\Model\DataObject\Proposition;
use App\SAE\Model\DataObject\Vote;
use App\SAE\Model\HTTP\Session;
use App\SAE\Model\Repository\PropositionRepository;
use App\SAE\Model\Repository\QuestionRepository;
use App\SAE\Model\Repository\VoteRepository;
use App\SAE\Model\Repository\UtilisateurRepository;

class VoteController extends MainController
{
    public static function voter() {

        $session = Session::getInstance();

        if(!$session->contient("idUtilisateur")) {
            static::error("afficherAccueil", "Vous devez être connecté pour voter");
        }

        if(!isset($_GET['idProposition']) || !is_numeric($_GET['idProposition'])) {
            static::error("afficherAccueil", "Aucune proposition n'a été sélectionnée");
            return;
        }
        $idProposition = intval($_GET['idProposition']);

        $proposition = Proposition::toProposition((new PropositionRepository)->select($idProposition));
        if(!$proposition) {
            static::error("afficherAccueil", "La proposition n'existe pas");
            return;
        }

        $idUtilisateur = $session->lire("idUtilisateur");

        $estVotant = (new QuestionRepository)->estVotant($proposition->getQuestion()->getIdQuestion(), $idUtilisateur);
        if(!$estVotant) {
            static::error("afficherAccueil", "Vous n'êtes pas votant pour cette question");
            return;
        }

        $exists = (new VoteRepository)->select($idUtilisateur, $idProposition);
        if($exists) {
            static::error("afficherAccueil", "Vous avez déjà voté pour cette proposition");
            return;
        }

        $votant = (new UtilisateurRepository())->select($idUtilisateur);
        $vote = new Vote($proposition, $votant, 1);
        (new VoteRepository)->insert($vote);

    static::message("afficherAccueil", "Votre vote a bien été pris en compte");
    }
}
