<?php

namespace App\SAE\Controller;

use App\SAE\Model\DataObject\Proposition;
use App\SAE\Model\DataObject\Vote;
use App\SAE\Model\HTTP\Session;
use App\SAE\Model\Repository\PropositionRepository;
use App\SAE\Model\Repository\VoteRepository;

class VoteController extends MainController
{
    public static function voter() {

        $session = Session::getInstance();

        if(!$session->contient("idUtilisateur")) {
            static::error("afficherAccueil", "Vous devez être connecté pour voter");
        }

        if(!isset($_GET['idProposition']) || !is_numeric($_GET['idProposition'])) {
            static::error("listerPropositions", "Aucune proposition n'a été sélectionnée");
            return;
        }
        $idProposition = intval($_GET['idProposition']);
        $proposition = Proposition::toProposition((new PropositionRepository)->select($idProposition));
        if(!$proposition) {
            static::error("listerPropositions", "La proposition n'existe pas");
            return;
        }
        $idUtilisateur = $session->lire("idUtilisateur");

        $exists = (new VoteRepository)->select($idUtilisateur, $idProposition);

        if($exists) {
            static::error("listerPropositions", "Vous avez déjà voté pour cette proposition");
            return;
        }

        $vote = new Vote($proposition, $idUtilisateur, 1);
        (new VoteRepository)->insert($vote);
    }
}
