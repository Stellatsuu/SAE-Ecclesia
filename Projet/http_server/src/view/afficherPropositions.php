<?php
$propositionActuelle = $propositions[$index];
$nbPropopositions = count($propositions);
?>

<div class="panel" id="afficherPropositions">
    <h1><?= $question->getTitre() ?></h1>
    <p id="description"><?= $question->getDescription() ?></p>

    <div id="propositionSelector">
        <a href="frontController.php?controller=proposition&action=afficherPropositions&idQuestion=<?=rawurlencode($question->getIdQuestion())?>&index=<?=$index==0 ? $nbPropopositions-1 : $index-1?>">
            <img src="assets/images/arrow.svg" style="transform: rotate(90deg)">
        </a>
        <span class="boite"><?= $propositionActuelle->getTitreProposition();?></span>
        <a href="frontController.php?controller=proposition&action=afficherPropositions&idQuestion=<?=rawurlencode($question->getIdQuestion())?>&index=<?=($index+1)%$nbPropopositions?>">
            <img src="assets/images/arrow.svg" style="transform: rotate(-90deg)">
        </a>
    </div>

    <div class="boite" id="proposition">
        <?php
            foreach ($propositionActuelle->getParagraphes() as $paragraphe){
                echo "<h2>".$paragraphe->getSection()->getNomSection()."</h2>";
                echo "<p>".$paragraphe->getContenuParagraphe()."</p>";
            }
        ?>
    </div>
</div>