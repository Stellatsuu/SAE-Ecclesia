<?php

namespace App\SAE\Model\SystemeVote;

use App\SAE\Controller\VoteController;
use App\SAE\Lib\ConnexionUtilisateur;
use App\SAE\Model\DataObject\Proposition;
use App\SAE\Model\DataObject\Vote;
use App\SAE\Model\Repository\PropositionRepository;
use App\SAE\Model\Repository\VotantRepository;
use App\SAE\Model\Repository\VoteRepository;

use const App\SAE\Controller\ACCUEIL_URL;

class VoteParApprobation extends AbstractSystemeVote
{

    public function getNom(): string
    {
        return "approbation";
    }

    public function getNomComplet(): string
    {
        return "vote par approbation";
    }

    public function afficherInterfaceVote(): string
    {
        $username = ConnexionUtilisateur::getUsernameSiConnecte();
        $question = $this->getQuestion();
        $propositions = (new PropositionRepository)->selectAllByQuestion($question->getIdQuestion());
        $aDejaVote = (new VoteRepository)->existsForQuestion($question->getIdQuestion(), $username);
        if ($aDejaVote) {
            $votes = (new VoteRepository)->selectAllByQuestionEtVotant($question->getIdQuestion(), $username);
            $idPropositionVotees = [];
            foreach ($votes as $vote) {
                $idPropositionVotees[] = $vote->getIdProposition();
            }
        }

        $propositionCheckboxes = [];
        for ($i = 0; $i < count($propositions); $i++) {
            $p = $propositions[$i];
            $idProposition = rawurlencode($p->getIdProposition());
            $titreProposition = htmlspecialchars($p->getTitreProposition());

            $checked = $aDejaVote && in_array($idProposition, $idPropositionVotees) ? "checked" : "";

            $checkbox = <<<HTML
                <label for="choix$idProposition">$titreProposition</label>
                <input type="checkbox" id="choix$idProposition" name="idPropositions[]" value="$idProposition" $checked>
            HTML;

            $propositionCheckboxes[] = $checkbox;
        }

        $propositionCheckboxes = implode("", $propositionCheckboxes);
        $submit = $aDejaVote ? "Modifier mon vote" : "Voter";

        $res = <<<HTML
        <p>Le vote se déroule en 1 tour. Choisissez toutes les propositions que vous soutenez.</p>
        <div class="choix-proposition">
            $propositionCheckboxes
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

        $votes = (new VoteRepository)->selectAllByQuestion($question->getIdQuestion());
        $usernamesVotant = array_map(function (Vote $v) {
            return $v->getUsernameVotant();
        }, $votes);
        $usernamesVotant = array_unique($usernamesVotant);
        $nbTotalVotes = count($usernamesVotant) ?: 1;

        $resultats = $this->calculerResultats($votes);

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

    private function calculerResultats($votes): array
    {
        $question = $this->getQuestion();
        $propositions = (new PropositionRepository)->selectAllByQuestion($question->getIdQuestion());

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
        $question = $this->getQuestion();
        $idQuestion = $question->getIdQuestion();
        $AP_URL = "frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestion";

        $estVotant = (new VotantRepository)->existsForQuestion($idQuestion, $username);
        if (!$estVotant)
            VoteController::error($AP_URL, "Vous n'êtes pas votant pour cette question");

        $idPropositions = $_POST["idPropositions"] ?? [];
        if (!is_array($idPropositions)) {
            VoteController::error($AP_URL, "Erreur lors du vote 1");
        }

        $propositions = (new PropositionRepository)->selectAllByQuestion($idQuestion);
        $idPropositionsBDD = array_map(function (Proposition $p) {
            return $p->getIdProposition();
        }, $propositions);


        $votes = [];
        foreach ($idPropositions as $idProposition) {
            $proposition = null;
            foreach ($propositions as $p) {
                if ($p->getIdProposition() == $idProposition) {
                    $proposition = $p;
                    break;
                }
            }

            $proposition = Proposition::castIfNotNull($proposition, $AP_URL, "Erreur lors du vote 2");

            $votes[] = new Vote($proposition, $username, 1);
        }

        $aDejaVote = (new VoteRepository)->existsForQuestion($idQuestion, $username);
        if ($aDejaVote) {
            (new VoteRepository)->deleteAllByQuestionEtVotant($idQuestion, $username);
            if (empty($votes)) {
                $message = "Votre vote a bien été supprimé";
            } else {
                foreach ($votes as $vote) {
                    (new VoteRepository)->insert($vote);
                }
                $message = "Votre vote a bien été modifié";
            }
        } else {
            foreach ($votes as $vote) {
                (new VoteRepository)->insert($vote);
            }
            $message = "Votre vote a bien été pris en compte";
        }

        VoteController::message($AP_URL, $message);
    }
}
