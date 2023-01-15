<?php

//$dataQuestion
$idQuestion = $dataQuestion['idQuestion'];
$titre = htmlspecialchars($dataQuestion['titre']);
$description = htmlspecialchars($dataQuestion['description']);
$tags = $dataQuestion['tags'];
$sections = $dataQuestion['sections'];
$systemeVote = $dataQuestion['systemeVote'];

$dateDebutRedaction = $dataQuestion['dateDebutRedaction'];
$heureDebutRedaction = $dataQuestion['heureDebutRedaction'];
$dateFinRedaction = $dataQuestion['dateFinRedaction'];
$heureFinRedaction = $dataQuestion['heureFinRedaction'];
$dateOuvertureVotes = $dataQuestion['dateOuvertureVotes'];
$heureOuvertureVotes = $dataQuestion['heureOuvertureVotes'];
$dateFermetureVotes = $dataQuestion['dateFermetureVotes'];
$heureFermetureVotes = $dataQuestion['heureFermetureVotes'];

echo '<pre>';
print_r($dataQuestion);
echo '</pre>';

?>

<form method="post" action="frontController.php?controller=question&action=afficherFormulairePoserQuestion&page=roles" class="panel" id="poserQuestion">
    <h1>Posez votre question :</h1>

    <div id="poser-question__top">
        <div id="progress-bar">
            <div class="progress-bar__step filling"></div>
            <div class="progress-bar__step filling"></div>
            <div class="progress-bar__step filling"></div>
            <div class="progress-bar__step filling"></div>
            <div class="progress-bar__step"></div>
        </div>

        <h2>Calendrier</h2>
        <p>
            Choisissez les dates clé du calendrier de la question
        </p>

        <label>Phase de rédaction : </label>
        <div class="conteneurDateHeure">
            <div>
                <span>
                    Du
                    <input required type="date" name="dateDebutRedaction" value="<?= $dateDebutRedaction ?>">
                </span>
                <span>
                    à
                    <input required type="time" name="heureDebutRedaction" value="<?= $heureDebutRedaction ?>">
                </span>
            </div>
            <div>
                <span>
                    au
                    <input required type="date" name="dateFinRedaction" value="<?= $dateFinRedaction ?>">
                </span>
                <span>
                    à
                    <input required type="time" name="heureFinRedaction" value="<?= $heureFinRedaction ?>">
                </span>
            </div>
        </div>

        <label>Phase de votes : </label>
        <div class="conteneurDateHeure">
            <div>
                <span>
                    Du
                    <input required type="date" name="dateOuvertureVotes" value="<?= $dateOuvertureVotes ?>">
                </span>
                <span>
                    à
                    <input required type="time" name="heureOuvertureVotes" value="<?= $heureOuvertureVotes ?>">
                </span>
            </div>
            <div>
                <span>
                    au
                    <input required type="date" name="dateFermetureVotes" value="<?= $dateFermetureVotes ?>">
                </span>
                <span>
                    à
                    <input required type="time" name="heureFermetureVotes" value="<?= $heureFermetureVotes ?>">
                </span>
            </div>
        </div>
    </div>

    <div id="poser-question__body">
        
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
    <input type="hidden" name="systemeVote" value="<?= $systemeVote ?>">







    <div id="poser-question__bottom">
        <button type="submit">
            Suivant
        </button>
    </div>
</form>