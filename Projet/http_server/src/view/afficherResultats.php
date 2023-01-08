<?php

use App\SAE\Lib\Markdown;
use App\SAE\Model\DataObject\Question;

$question = Question::castIfNotNull($question);
$organisateur = $question->getOrganisateur();
$systemeVote = $question->getSystemeVote();

$titreQuestion = htmlspecialchars($question->getTitre());
$descriptionQuestion = Markdown::toHtml($question->getDescription());
$nomUsuelOrga = htmlspecialchars($organisateur->getNomUsuel());

$resultats = $systemeVote->afficherResultats();

if($resultats == "") {
    $resultats = <<<HTML
    <img src="assets/images/confused-cat.gif" alt="confused cat" class="resultats-vote__body__noresults">

    <h2 class="resultats-vote__body__noresults">Pas de résultats sans propositions.</h2>
    HTML;
}

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
        <div id="description" class="markdown"><?= $descriptionQuestion ?></div>
    </div>

    <div id="afficher-resultats__body" class="panel2">

        <div id="resultats-vote__top">
            <h2>Résultats du vote</h2>
        </div>

        <div id="resultats-vote__body">
            <?= $resultats ?>
        </div>

    </div>
</div>
