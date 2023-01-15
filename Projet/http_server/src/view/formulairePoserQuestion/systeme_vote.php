<?php

//$dataQuestion
$idQuestion = $dataQuestion['idQuestion'];
$titre = htmlspecialchars($dataQuestion['titre']);
$description = htmlspecialchars($dataQuestion['description']);
$tags = $dataQuestion['tags'];
$sections = $dataQuestion['sections'];

echo '<pre>';
print_r($dataQuestion);
echo '</pre>';

?>

<form method="post" action="frontController.php?controller=question&action=afficherFormulairePoserQuestion&page=calendrier" class="panel">
    <h1>Posez votre question :</h1>

    <div id="poser-question__top">
        <div id="progress-bar">
            <span class="progress-bar__step filling"></span>
            <span class="progress-bar__step"></span>
            <span class="progress-bar__step"></span>
            <span class="progress-bar__step"></span>
            <span class="progress-bar__step"></span>
        </div>

        <h2>Choix du système de vote</h2>
        <p>
            Choisissez le système de vote que vous souhaitez utiliser pour votre question.
        </p>
    </div>

    <div id="poser-question__body">
        <label for="systeme_vote_majoritaire_a_un_tour">Majoritaire à un tour</label>
        <input type="radio" name="systemeVote" value="majoritaire_a_un_tour" id="systeme_vote_majoritaire_a_un_tour" required>

        <label for="systeme_vote_approbation">Vote par approbation</label>
        <input type="radio" name="systemeVote" value="approbation" id="systeme_vote_approbation" required>

        <label for="systeme_vote_alternatif">Vote alternatif</label>
        <input type="radio" name="systemeVote" value="alternatif" id="systeme_vote_alternatif" required>

        <label for="jugement_majoritaire">Jugement majoritaire</label>
        <input type="radio" name="systemeVote" value="jugement_majoritaire" id="jugement_majoritaire" required>
    </div>

    <input type="hidden" name="idQuestion" value="<?= $idQuestion ?>">

    <input type="hidden" name="description" value="<?= $description ?>">
    <input type="hidden" name="tags" value="<?= $tags ?>">
    <?php
    $i = 1;
    foreach ($sections as $section) {
        $titre = htmlspecialchars($section['titre']);
        $description = htmlspecialchars($section['description']);
        echo "<input type='hidden' name='sections[$i][titre]' value='$titre'>";
        echo "<input type='hidden' name='sections[$i][description]' value='$description'>";
        $i++;
    }
    ?>

    <div id="poser-question__bottom">
        <button type="submit">
            Suivant
        </button>
    </div>
</form>