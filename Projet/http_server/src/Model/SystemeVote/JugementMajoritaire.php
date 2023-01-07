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

class JugementMajoritaire extends AbstractSystemeVote
{

    private const MENTIONS = [
        0 => "À rejeter",
        1 => "Insuffisant",
        2 => "Passable",
        3 => "Assez bien",
        4 => "Bien",
        5 => "Très bien",
    ];

    public function getNom(): string
    {
        return "jugement_majoritaire";
    }

    public function getNomComplet(): string
    {
        return "jugement majoritaire";
    }

    public function afficherInterfaceVote(): string
    {
        $username = ConnexionUtilisateur::getUsernameSiConnecte();
        $question = $this->getQuestion();
        $idQuestion = rawurlencode($question->getIdQuestion());
        $propositions = (new PropositionRepository)->selectAllByQuestion($question->getIdQuestion());

        $aDejaVote = (new VoteRepository)->existsForQuestion($idQuestion, $username);
        if ($aDejaVote) {
            $votesBDD = (new VoteRepository)->selectAllByQuestionEtVotant($idQuestion, $username);
            $mentionsBDD = [];
            foreach ($votesBDD as $vote) {
                $vote = Vote::castIfNotNull($vote);
                $mentionsBDD[$vote->getIdProposition()] = $vote->getValeur();
            }
        } else {
            $mentionsBDD = [];
        }

        $propositionHTMLs = [];
        for ($i = 0; $i < count($propositions); $i++) {
            $p = Proposition::castIfNotNull($propositions[$i]);
            $idProposition = rawurlencode($p->getIdProposition());
            $titreProposition = htmlspecialchars($p->getTitreProposition());
            $lienProposition = "frontController.php?action=afficherPropositions&idQuestion=$idQuestion&index=$i";

            $mentionsTD = [];
            for ($j = 0; $j < count(self::MENTIONS); $j++) {
                $checked = $aDejaVote && $mentionsBDD[$idProposition] == $j ? "checked" : "";
                $mentionsTD[] = "<td><input type='radio' name='mentions[$idProposition]' value='$j' $checked></td>";
            }
            $mentionsTD = implode("", $mentionsTD);

            $propositionHTML = <<<HTML
                <tr>
                    <td><a href="$lienProposition">$titreProposition</a></td>
                    $mentionsTD
                </tr>
            HTML;

            $propositionHTMLs[] = $propositionHTML;
        }
        $propositionHTMLs = implode("", $propositionHTMLs);

        $mentionsTH = [];
        for ($i = 0; $i < count(self::MENTIONS); $i++) {
            $mentionsTH[] = "<th>" . self::MENTIONS[$i] . "</th>";
        }
        $mentionsTH = implode("", $mentionsTH);

        $submitValue = $aDejaVote ? "Modifier mon vote" : "Voter";

        $res = <<<HTML
            <p>Le vote se déroule en 1 tour. Veuillez attribuer une note à chaque proposition.</p>
            <table class="propositions">
                <tr>
                    <th>Propositions</th>
                    $mentionsTH
                </tr>

                $propositionHTMLs

            </table>

            <input type="submit" value="$submitValue">
        HTML;

        return $res;
    }

    public function afficherResultats(): string
    {
        $question = $this->getQuestion();
        $idQuestion = $question->getIdQuestion();
        $propositions = (new PropositionRepository)->selectAllByQuestion($idQuestion);
        $votes = (new VoteRepository)->selectAllByQuestion($idQuestion);
        $histogramme = $this->calculerHistogramme($propositions, $votes);
        $idPropositionsTriees = static::trier($histogramme);

        $propositionHTMLs = [];
        $diagrammeHTMLs = [];

        foreach ($idPropositionsTriees as $idProposition) {

            //trouver la proposition correspondante
            $p = null;
            foreach ($propositions as $proposition) {
                $proposition = Proposition::castIfNotNull($proposition);
                if ($proposition->getIdProposition() == $idProposition) {
                    $p = $proposition;
                    break;
                }
            }
            $p = Proposition::castIfNotNull($p);

            $titreProposition = htmlspecialchars($p->getTitreProposition());
            $lienProposition = "TODO";

            $mentions = $histogramme[$idProposition];
            $nbMentions = array_sum($mentions);
            $mentionsPourcent = array_map(
                function ($mention) use ($nbMentions) {
                    return round($mention / $nbMentions * 100, 1);
                },
                $mentions
            );

            $diagrammeHTML = <<<HTML
                <div class="diagramme__ligne">
                    <div class="diagramme__segment" style="width: {$mentionsPourcent[0]}%">
                        <div class="diagramme__segment__texte">{$mentionsPourcent[0]}%</div>

                    </div>
                    <div class="diagramme__segment" style="width: {$mentionsPourcent[1]}%">
                        <div class="diagramme__segment__texte">{$mentionsPourcent[1]}%</div>
                    </div>
                    <div class="diagramme__segment" style="width: {$mentionsPourcent[2]}%">
                        <div class="diagramme__segment__texte">{$mentionsPourcent[2]}%</div>
                    </div>
                    <div class="diagramme__segment" style="width: {$mentionsPourcent[3]}%">
                        <div class="diagramme__segment__texte">{$mentionsPourcent[3]}%</div>
                    </div>
                    <div class="diagramme__segment" style="width: {$mentionsPourcent[4]}%">
                        <div class="diagramme__segment__texte">{$mentionsPourcent[4]}%</div>
                    </div>
                    <div class="diagramme__segment" style="width: {$mentionsPourcent[5]}%">
                        <div class="diagramme__segment__texte">{$mentionsPourcent[5]}%</div>
                    </div>
                </div>
            HTML;

            $propositionHTML = <<<HTML
                <a href="$lienProposition">$titreProposition</a>
                $diagrammeHTML
            HTML;

            $propositionHTMLs[] = $propositionHTML;
        }

        $propositionHTMLs = implode("", $propositionHTMLs);

        $propositionGagnante = null;
        foreach ($propositions as $proposition) {
            $proposition = Proposition::castIfNotNull($proposition);
            if ($proposition->getIdProposition() == $idPropositionsTriees[0]) {
                $propositionGagnante = $proposition;
                break;
            }
        }
        $propositionGagnante = Proposition::castIfNotNull($propositionGagnante);
        $titrePropositionGagnante = htmlspecialchars($propositionGagnante->getTitreProposition());
        $mentionMajoritairePropositionGagnante = static::MENTIONS[static::mentionMajoritaire($histogramme[$idPropositionsTriees[0]])];

        $res = <<<HTML
            <div class="jugement-majoritaire">
                <div class="jugement-majoritaire__grid">
                    $propositionHTMLs
                </div>

                <p><strong>$titrePropositionGagnante</strong> est la proposition gagnante, avec une mention majoritaire de <strong>$mentionMajoritairePropositionGagnante</strong>.</p>

                <div class="jugement-majoritaire__legende">
                    <h3>Mentions</h3>
                    <div class="diagramme__legende">
                        <span></span>
                        : <strong>Très bien</strong>
                    </div>
                    <div class="diagramme__legende">
                        <span></span>
                        : <strong>Bien</strong>
                    </div>
                    <div class="diagramme__legende">
                        <span></span>
                        : <strong>Assez bien</strong>
                    </div>
                    <div class="diagramme__legende">
                        <span></span>
                        : <strong>Passable</strong>
                    </div>
                    <div class="diagramme__legende">
                        <span></span>
                        : <strong>Insuffisant</strong>
                    </div>
                    <div class="diagramme__legende">
                        <span></span>
                        : <strong>À rejeter</strong>
                    </div>
                </div>

            </div>
        HTML;

        return $res;
    }

    public function traiterVote(): void
    {
        $question = $this->getQuestion();
        $idQuestion = rawurlencode($question->getIdQuestion());
        $username = ConnexionUtilisateur::getUsernameSiConnecte();
        $propositions = (new PropositionRepository)->selectAllByQuestion($question->getIdQuestion());
        $idPropositionBDD = array_map(function ($p) {
            return $p->getIdProposition();
        }, $propositions);
        $mentions = $_POST["mentions"];
        $lienAfficherPropositions = "frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestion";

        $votes = [];
        foreach ($mentions as $idProposition => $mention) {

            if (!in_array($idProposition, $idPropositionBDD)) {
                VoteController::error($lienAfficherPropositions, "Cette proposition n'existe pas ou n'est pas associée à cette question.");
            }

            if (!is_numeric($mention) || $mention < 0 || $mention >= count(self::MENTIONS)) {
                VoteController::error($lienAfficherPropositions, "Mention invalide.");
            }

            $vote = new Vote(
                $idProposition,
                $username,
                $mention
            );

            $votes[] = $vote;
        }

        if (count($votes) != count($propositions)) {
            VoteController::error($lienAfficherPropositions, "Vous n'avez pas voté pour toutes les propositions.");
        }

        $aDejaVote = (new VoteRepository)->existsForQuestion($idQuestion, $username);
        if ($aDejaVote) {
            (new VoteRepository)->deleteAllByQuestionEtVotant($idQuestion, $username);
            $message = "Votre vote a bien été modifié.";
        } else {
            $message = "Votre vote a bien été pris en compte.";
        }

        foreach ($votes as $vote) {
            (new VoteRepository)->insert($vote);
        }

        VoteController::message($lienAfficherPropositions, $message);
    }

    private static function calculerHistogramme(array $propositions, array $votes): array
    {
        $histogramme = [];
        foreach ($propositions as $p) {
            $p = Proposition::castIfNotNull($p);
            $histogramme[$p->getIdProposition()] = array_fill(0, count(self::MENTIONS), 0);
        }

        foreach ($votes as $v) {
            $v = Vote::castIfNotNull($v);
            $histogramme[$v->getIdProposition()][$v->getValeur()]++;
        }

        return $histogramme;
    }

    private static function trier($histogramme): array
    {
        $idPropositions = array_keys($histogramme);
        usort($idPropositions, function ($a, $b) use ($histogramme) {
            return static::comparer($histogramme[$a], $histogramme[$b]);
        });

        return $idPropositions;
    }

    private static function comparer($a, $b): int
    {
        do {
            $ma = static::mentionMajoritaire($a);
            $mb = static::mentionMajoritaire($b);

            if ($ma > $mb) {
                return -1;
            } else if ($ma < $mb) {
                return 1;
            } else {
                $a[$ma]--;
                $b[$mb]--;
            }

            if (array_sum($a) == 0 || array_sum($b) == 0) {
                return 0;
            }
        } while ($ma == $mb);
    }

    private static function mentionMajoritaire(array $mentions): int
    {
        $nbVotes = array_sum($mentions);
        if ($nbVotes % 2 == 0) {
            $nbVotesMentionMajoritaire = $nbVotes / 2;
        } else {
            $nbVotesMentionMajoritaire = ($nbVotes - 1) / 2 + 1;
        }

        $nbVotesCumules = 0;
        for ($i = 0; $i < count($mentions); $i++) {
            $nbVotesCumules += $mentions[$i];
            if ($nbVotesCumules >= $nbVotesMentionMajoritaire) {
                return $i;
            }
        }
    }
}
