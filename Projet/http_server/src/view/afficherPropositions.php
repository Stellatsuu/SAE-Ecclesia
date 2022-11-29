<?php


use App\SAE\Lib\PhaseQuestion;
use App\SAE\Model\DataObject\Question;

$propositionActuelle = $propositions[$index];
$nbPropopositions = count($propositions);
$question = Question::castIfNotNull($question);
?>

<div class="panel" id="afficherPropositions">
    <input type="checkbox" id="questionDescription" class="texteDepliantTrigger"/>
    <div id="propositionTitle">
        <h1 class="title"><?= $question->getTitre() ?></h1>
        <label for="questionDescription">
            <img src='./assets/images/arrow.svg' class='arrow' alt='open and close arrow'/>
        </label>
    </div>
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
    <div id="btns">
    <?php
        if($question->getPhase() == PhaseQuestion::Lecture)
            echo <<<HTML
            <a class="button" href="frontController.php?controller=proposition&action=supprimerProposition&idProposition=<?= $propositionActuelle->getIdProposition() ?>">Supprimer</a>
            HTML;
        if($question->getPhase() == PhaseQuestion::Redaction)
            echo <<<HTML
            <a class="button" href="frontController.php?controller=proposition&action=afficherFormulaireContribuerProposition&idProposition={$propositionActuelle->getIdProposition()}">Modifier</a>
            HTML;
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