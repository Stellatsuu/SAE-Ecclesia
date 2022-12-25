<?php

namespace App\SAE\Model\SystemeVote;

use App\SAE\Controller\MainController;
use App\SAE\Controller\VoteController;
use App\SAE\Lib\ConnexionUtilisateur;
use App\SAE\Lib\PhaseQuestion;
use App\SAE\Model\DataObject\Proposition;
use App\SAE\Model\DataObject\Question;
use App\SAE\Model\DataObject\Vote;
use App\SAE\Model\Repository\PropositionRepository;
use App\SAE\Model\Repository\VotantRepository;
use App\SAE\Model\Repository\VoteRepository;

use const App\SAE\Controller\ACCUEIL_URL;

class UninominalMajoritaireAUnTour extends AbstractSystemeVote
{


    public function getNom(): string
    {
        return "majoritaire_a_un_tour";
    }

    public function getNomComplet(): string
    {
        return "scrutin uninominal majoritaire à un tour";
    }

    public function afficherInterfaceVote(): string
    {
        $username = ConnexionUtilisateur::getUsernameSiConnecte();
        $question = $this->getQuestion();
        $propositions = (new PropositionRepository)->selectAllByQuestion($question->getIdQuestion());
        $aDejaVote = (new VoteRepository)->existsForQuestion($question->getIdQuestion(), $username);
        if($aDejaVote){
            $votes = (new VoteRepository)->selectAllByQuestionEtVotant($question->getIdQuestion(), $username);
            $idPropositionVotee = $votes[0]->getIdProposition();
        }

        $propositionRadios = [];
        foreach ($propositions as $p) {
            $idProposition = rawurlencode($p->getIdProposition());
            $titreProposition = htmlspecialchars($p->getTitreProposition());

            $checked = $aDejaVote && $idProposition == $idPropositionVotee ? "checked" : "";

            $radio = <<<HTML
                <label for="choix$idProposition">$titreProposition</label>
                <input type="radio" name="idProposition" id="choix$idProposition" value="$idProposition" required $checked>
            HTML;

            $propositionRadios[] = $radio;
        }

        $propositionRadios = implode("", $propositionRadios);
        $submit = $aDejaVote ? "Modifier mon vote" : "Voter";


        $res = <<<HTML
        <p>Le vote se déroule en 1 tour. Choisissez une unique proposition parmi les suivantes.</p>
        <div class="choix-proposition">
            $propositionRadios
        </div>

            <input type="submit" value="$submit">
        HTML;

        return $res;
    }

    public function afficherResultats(): string
    {
        $question = $this->getQuestion();
        $idQuestion = rawurlencode($question->getIdQuestion());
        $propositions = (new PropositionRepository)->selectAllByQuestion($question->getIdQuestion());

        $resultats = $this->calculerResultats();
        $nbTotalVotes = array_sum($resultats) ?: 1;

        $res = [];
        for ($i = 0; $i < count($propositions); $i++) {
            $p = $propositions[$i];
            $idProposition = rawurlencode($p->getIdProposition());
            $titreProposition = htmlspecialchars($p->getTitreProposition());

            $nbVotes = $resultats[$idProposition];
            $pourcents = round($nbVotes / $nbTotalVotes * 100, 0);

            $html = <<<HTML
                <div>
                    <div class="percentage_bar" style='--percentage: $pourcents%'></div>
                    <label><a href="frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestion&index=$i">$titreProposition</a></label>
                    <span class="vote">$pourcents %</span>
                </div>
                HTML;

            $res[$html] = $pourcents;
        }

        arsort($res);

        $res = implode("", array_keys($res));

        return $res;
    }

    private function calculerResultats(): array
    {
        $question = $this->getQuestion();
        $propositions = (new PropositionRepository)->selectAllByQuestion($question->getIdQuestion());

        $votes = (new VoteRepository)->selectAllByQuestion($question->getIdQuestion());

        $resultats = [];
        foreach ($propositions as $p) {
            $resultats[$p->getIdProposition()] = 0;
        }

        foreach ($votes as $v) {
            $resultats[$v->getIdProposition()]++;
        }

        return $resultats;
    }

    public function traiterVote(): void
    {
        $username = ConnexionUtilisateur::getUsernameSiConnecte();

        $idProposition = VoteController::getIfSetAndNumeric("idProposition");
        $proposition = Proposition::castIfNotNull((new PropositionRepository)->select($idProposition));

        $question = $proposition->getQuestion();
        $idQuestion = $question->getIdQuestion();
        $phase = $question->getPhase();
        if ($phase !== PhaseQuestion::Vote) {
            VoteController::error("frontController.php", "La question n'est pas en phase de vote");
            return;
        }

        /**
         * @var string URL de afficherPropositions
         */
        $AP_URL = "frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestion";

        $estVotant = (new VotantRepository)->existsForQuestion($question->getIdQuestion(), $username);
        if (!$estVotant) {
            VoteController::error(ACCUEIL_URL, "Vous n'êtes pas votant pour cette question");
        }

        $aDejaVote = (new VoteRepository)->existsForQuestion($proposition->getIdQuestion(), $username);
        if ($aDejaVote) {
            $message = "Votre vote a bien été modifié";
            (new VoteRepository)->deleteAllByQuestionEtVotant($proposition->getIdQuestion(), $username);
        } else {
            $message = "Votre vote a bien été pris en compte";
        }

        $vote = new Vote($proposition, $username, 1);

        (new VoteRepository)->insert($vote);

        VoteController::message($AP_URL, $message);
    }
}
