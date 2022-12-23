<?php

use App\SAE\Model\DataObject\Proposition;
use App\SAE\Model\DataObject\Question;

$question = Question::castIfNotNull($question);
$systemeVote = $question->getSystemeVote();
$resultats = $systemeVote->afficherResultats();

?>

<div class="panel" id="afficherResultats">
    <h1><?= $question->getTitre() ?> - Résultats</h1>
    <p id="description"><?= $question->getDescription() ?></p>

    <div id="resultats_vote">
        <h2>Résultats des votes</h2>

        <div>
            
            <?= $resultats ?>

        </div>
    </div>
</div>