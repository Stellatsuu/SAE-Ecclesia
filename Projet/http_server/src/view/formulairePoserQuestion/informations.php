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

<form method="post" action="frontController.php?controller=question&action=afficherFormulairePoserQuestion&page=plan" class="panel" id="poserQuestion">
    <h1>Posez votre question :</h1>

    <div id="poser-question__top">
        <div id="progress-bar">
            <div class="progress-bar__step filling"></div>
            <div class="progress-bar__step"></div>
            <div class="progress-bar__step"></div>
            <div class="progress-bar__step"></div>
            <div class="progress-bar__step"></div>
            <div class="progress-bar__step"></div>
        </div>

        <h2>Informations sur la question</h2>
        <p>
            Remplissez tout ce qu'il y a à savoir sur votre question.
        </p>
    </div>

    <div id="poser-question__body">
        <label for="titre_id">
            Question :
        </label>
        <input readonly type="text" name="titre" id="titre_id" value="<?= $titre ?>" required>

        <label for="description_id">
            Description :
        </label>
        <div class="text_input_div">
            <textarea rows=6 cols=50 id="description_id" name="description" maxlength="4000" required><?= $description ?></textarea>
            <span class="indicateur_max_chars unselectable">4000 max</span>
        </div>

        <label>Tags :</label>
        <div id="tags_input">
            <input type="text" name="newtag" id="newtag_id" pattern="^[a-zA-Z0-9]+$">
            <button type="button" id="add_tag">+</button>
            <span id="erreur"></span>
            <input type="hidden" name="tags" value="{check}">
        </div>

        <div id="tags_list" class="tags-list">

        </div>
    </div>

    <input type="hidden" name="idQuestion" value="<?= $idQuestion ?>">

    <div id="poser-question__bottom">
        <button type="submit">
            Suivant
        </button>
    </div>
</form>

<script src="js/formulairePoserQuestions_tags.js"></script>
