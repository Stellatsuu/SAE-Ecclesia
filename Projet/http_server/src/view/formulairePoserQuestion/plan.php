<?php

//$dataQuestion
$idQuestion = $dataQuestion['idQuestion'];
$titre = htmlspecialchars($dataQuestion['titre']);
$description = htmlspecialchars($dataQuestion['description']);
$tags = $dataQuestion['tags'];

echo '<pre>';
print_r($dataQuestion);
echo '</pre>';

?>

<form method="post" action="frontController.php?controller=question&action=afficherFormulairePoserQuestion&page=systeme_vote" class="panel" id="poserQuestion">
    <h1>Posez votre question :</h1>

    <div id="poser-question__top">
        <div id="progress-bar">
            <div class="progress-bar__step filling"></div>
            <div class="progress-bar__step filling"></div>
            <div class="progress-bar__step"></div>
            <div class="progress-bar__step"></div>
            <div class="progress-bar__step"></div>
            <div class="progress-bar__step"></div>
        </div>

        <h2>Plan de la question</h2>
        <p>
            C'est ici que vous définissez le plan de votre question.
            <br>
            Celle-ci se divise en plusieurs sections, chacune ayant un titre et une description.
        </p>
    </div>

    <div id="poser-question__body">
        <div id="sections_input">
            <button type="button" id="add_section">+</button>
        </div>
    </div>

    <input type="hidden" name="idQuestion" value="<?= $idQuestion ?>">

    <input type="hidden" name="description" value="<?= $description ?>">
    <input type="hidden" name="tags" value="<?= $tags ?>">

    <div id="poser-question__bottom">
        <button type="submit">
            Suivant
        </button>
    </div>
</form>

<script>
    const sections = <?= json_encode($dataQuestion['sections']) ?>;
</script>
<script src="js/formulairePoserQuestion_sections.js"></script>