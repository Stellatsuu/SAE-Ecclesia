<?php

use App\SAE\Model\DataObject\Proposition;
use App\SAE\Model\DataObject\Question;

$question = Question::castIfNotNull($question);
$idQuestion = rawurlencode($question->getIdQuestion());
$propositionGagnante = Proposition::castIfNotNull($propositionGagnante);
$propositions = array_map(function ($p) {
    return Proposition::castIfNotNull($p);
}, $propositions);

?>

<div class="panel" id="afficherResultats">
    <h1><?= $question->getTitre() ?> - Résultats</h1>
    <p id="description"><?= $question->getDescription() ?></p>

    <div id="resultats_vote">
        <h2>Résultats des votes</h2>

        <div>
            <?php

            $i = 0;
            foreach ($propositions as $p) {
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

                echo $html;
                $i++;
            }
            ?>
        </div>
    </div>
</div>