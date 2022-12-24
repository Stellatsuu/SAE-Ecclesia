<?php

use App\SAE\Lib\Markdown;
use App\SAE\Model\DataObject\Proposition;
use App\SAE\Model\DataObject\Question;

$question = Question::castIfNotNull($question);
$organisateur = $question->getOrganisateur();
$systemeVote = $question->getSystemeVote();

$titreQuestion = htmlspecialchars($question->getTitre());
$descriptionQuestion = htmlspecialchars($question->getDescription());
$nomUsuelOrga = htmlspecialchars($organisateur->getNomUsuel());

$resultats = $systemeVote->afficherResultats();

?>

<div class="panel" id="afficher-resultats">

    <div id="afficher-resultats__top">
        <h1><?= $titreQuestion ?>
            <span>
                par&nbsp;<?= $nomUsuelOrga ?>
            </span>
        </h1>
    </div>

    <div id="afficher-resultats__description" class="panel2">
        <h2>Description :</h2>
        <span id="description" class="markdown"><?= $descriptionQuestion ?></span>
    </div>

    <div id="resultats-vote">

        <div id="resultats_vote__top">
            <h2>Résultats du vote</h2>
        </div>

        <div id="resultats_vote__body">
            <?= $resultats ?>
        </div>

    </div>
</div>