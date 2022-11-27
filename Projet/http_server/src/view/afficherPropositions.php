<?php


use App\SAE\Lib\PhaseQuestion;
use App\SAE\Model\DataObject\Question;

$propositionActuelle = $propositions[$index];
$nbPropopositions = count($propositions);
$question = Question::toQuestion($question);
?>

<div class="panel" id="afficherPropositions">
    <h1><?= $question->getTitre() ?></h1>
    <p id="description"><?= $question->getDescription() ?></p>

    <div id="propositionSelector">
        <a href="frontController.php?controller=proposition&action=afficherPropositions&idQuestion=<?= rawurlencode($question->getIdQuestion()) ?>&index=<?= $index == 0 ? $nbPropopositions - 1 : $index - 1 ?>">
            <img src="assets/images/arrow.svg" style="transform: rotate(90deg)">
        </a>
        <span class="boite"><?= $propositionActuelle->getTitreProposition(); ?></span>
        <a href="frontController.php?controller=proposition&action=afficherPropositions&idQuestion=<?= rawurlencode($question->getIdQuestion()) ?>&index=<?= ($index + 1) % $nbPropopositions ?>">
            <img src="assets/images/arrow.svg" style="transform: rotate(-90deg)">
        </a>
    </div>

    <div class="boite" id="proposition">
        <?php
        foreach ($propositionActuelle->getParagraphes() as $paragraphe) {
            $nomSection = $paragraphe->getSection()->getNomSection();
            $contenu = $paragraphe->getContenuParagraphe();
            echo "<h2>$nomSection</h2>";
            echo "<p>$contenu</p>";
        }
        ?>
    </div>

    <form id="formulaire_vote" action="frontController.php?controller=vote&action=voter" method="post" style="<?= $question->getPhase() != PhaseQuestion::Vote ? 'display: none;' : '' ?>">

        <h2>Vote</h2>
        <p>Le vote se déroule en 1 tour. Choisissez une unique proposition parmi les suivantes</p>
        <div>
            <?php

            foreach ($propositions as $p) {
                $idProposition = rawurlencode($p->getIdProposition());
                $titreProposition = htmlspecialchars($p->getTitreProposition());

                $html = <<<HTML
                    <div for="choix$idProposition">
                        <label>$titreProposition</label>
                        <input type="radio" name="idProposition" id="choix$idProposition" value="$idProposition">
                    </div>
                HTML;

                echo $html;
            }
            ?>
        </div>
        <input type="submit" value="Voter">
    </form>








</div>