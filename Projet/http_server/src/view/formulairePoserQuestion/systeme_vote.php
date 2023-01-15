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

<form method="post" action="frontController.php?controller=question&action=afficherFormulairePoserQuestion&page=calendrier" class="panel" id="poserQuestion">
    <h1>Posez votre question :</h1>

    <div id="poser-question__top">
        <div id="progress-bar">
            <div class="progress-bar__step filling"></div>
            <div class="progress-bar__step filling"></div>
            <div class="progress-bar__step filling"></div>
            <div class="progress-bar__step"></div>
            <div class="progress-bar__step"></div>
        </div>

        <h2>Choix du système de vote</h2>
        <p>
            Choisissez le système de vote que vous souhaitez utiliser pour votre question.
        </p>
    </div>

    <div id="poser-question__body">
        <div id="systemesDeVote">
            <input type="radio" name="systemeVote" value="majoritaire_a_un_tour" id="systeme_vote_majoritaire_a_un_tour" required>
            <label for="systeme_vote_majoritaire_a_un_tour">
                <span class="systemesDeVote__titre">Majoritaire à un tour</span>
                <span class="systemesDeVote__descritpion">Le vote se déroule en 1 tour et en choisissant une unique proposition.</span>
            </label>

            <input type="radio" name="systemeVote" value="approbation" id="systeme_vote_approbation" required>
            <label for="systeme_vote_approbation">
                <span class="systemesDeVote__titre">Vote par approbation</span>
                <span class="systemesDeVote__descritpion">Le vote se déroule en 1 tour et en choisissant une ou plusieurs propositions.</span>
            </label>

            <input type="radio" name="systemeVote" value="alternatif" id="systeme_vote_alternatif" required>
            <label for="systeme_vote_alternatif">
                <span class="systemesDeVote__titre">Vote alternatif</span>
                <span class="systemesDeVote__descritpion">Le vote se déroule en plusieurs tours instantanés. Les votants votent une seule fois en classant les propositions par ordre de préférence.</span>
            </label>

            <input type="radio" name="systemeVote" value="jugement_majoritaire" id="jugement_majoritaire" required>
            <label for="jugement_majoritaire">
                <span class="systemesDeVote__titre">Jugement majoritaire</span>
                <span class="systemesDeVote__descritpion">Le vote se déroule en 1 tour et en attribuant une note a chacune des propositions.</span>
            </label>
        </div>
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