<?php

use App\SAE\Lib\Markdown;

//$dataQuestion
$titre = htmlspecialchars($dataQuestion['titre']);
$description = Markdown::toHtml($dataQuestion['description']);
$nomUsuelOrganisateur = htmlspecialchars($dataQuestion['nomUsuelOrganisateur']);
$resultats = $dataQuestion['resultats'];

if($resultats == "") {
    $resultats = <<<HTML
    <img src="assets/images/confused-cat.gif" alt="confused cat" class="resultats-vote__body__noresults">

    <h2 class="resultats-vote__body__noresults">Pas de résultats sans propositions.</h2>
    HTML;
}

?>

<div class="panel" id="afficher-resultats">

    <div id="afficher-resultats__top">
        <h1><?= $titre ?>
            <span>
                par&nbsp;<?= $nomUsuelOrganisateur ?>
            </span>
        </h1>
    </div>

    <div id="afficher-resultats__description" class="panel2">
        <h2>Description :</h2>
        <div id="description" class="markdown"><?= $description ?></div>
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
