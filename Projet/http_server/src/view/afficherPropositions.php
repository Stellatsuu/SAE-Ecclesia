<?php
$propositionActuelle = $propositions[$index];
$nbPropopositions = count($propositions);
?>

<div class="panel">
    <h1><?= $question->getTitre() ?></h1>
    <p><?= $question->getDescription() ?></p>

    <a href="frontController.php?controller=proposition&action=afficherPropositions&idQuestion=<?=rawurlencode($question->getIdQuestion())?>&index=<?=$index==0 ? $nbPropopositions-1 : $index-1?>">
        <img src="assets/images/arrow.svg" style="transform: rotate(90deg)">
    </a>
    <span class="boite"><?= $propositionActuelle->getTitreProposition();?></span>
    <a href="frontController.php?controller=proposition&action=afficherPropositions&idQuestion=<?=rawurlencode($question->getIdQuestion())?>&index=<?=($index+1)%$nbPropopositions?>">
        <img src="assets/images/arrow.svg" style="transform: rotate(-90deg)">
    </a>

    <div class="boite">
        <?php
            foreach ($propositionActuelle->getParagraphes() as $paragraphe){
                echo "<h2>".$paragraphe->getSection()->getNomSection()."</h2>";
                echo "<p>".$paragraphe->getContenuParagraphe()."</p>";
            }
        ?>
    </div>
</div>
