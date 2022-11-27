<?php

use App\SAE\Model\DataObject\Proposition;
use App\SAE\Model\DataObject\Question;

$question = Question::toQuestion($question);
$propositionGagnante = Proposition::toProposition($propositionGagnante);
$propositions = array_map(function ($p) {
    return Proposition::toProposition($p);
}, $propositions);

?>

<div class="panel" id="afficherPropositions">
    <h1><?= $question->getTitre() ?> - Résultats</h1>
    <p id="description"><?= $question->getDescription() ?></p>

    <div id="propositionSelector">
        <span class="boite"><?= $propositionGagnante->getTitreProposition(); ?></span>
    </div>

    <div class="boite" id="proposition">
        <?php
        foreach ($propositionGagnante->getParagraphes() as $paragraphe) {
            $nomSection = $paragraphe->getSection()->getNomSection();
            $contenu = $paragraphe->getContenuParagraphe();
            echo "<h2>$nomSection</h2>";
            echo "<p>$contenu</p>";
        }
        ?>
    </div>

    <div id="resultats_vote">
        <h2>Résultat du vote</h2>

        <div>
            <?php

            foreach ($propositions as $p) {
                $idProposition = rawurlencode($p->getIdProposition());
                $titreProposition = htmlspecialchars($p->getTitreProposition());
                $nbVotes = $resultats[$idProposition];
                $pourcents = round($nbVotes / $nbTotalVotes * 100, 0);

                $html = <<<HTML
                <div>
                    <label>$titreProposition</label>
                    <span class="vote">$pourcents %</span>
                </div>
                HTML;

                echo $html;
            }
            ?>
        </div>
    </div>
</div>