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

$redacteurs = $dataQuestion['redacteurs'];
$votants = $dataQuestion['votants'];

?>

<form method="post" action="frontController.php?controller=question&action=poserQuestion" class="panel" id="poserQuestion">
    <h1>Posez votre question :</h1>

    <div id="poser-question__top">
        <div id="progress-bar">
            <div class="progress-bar__step filling"></div>
            <div class="progress-bar__step filling"></div>
            <div class="progress-bar__step filling"></div>
            <div class="progress-bar__step filling"></div>
            <div class="progress-bar__step filling"></div>
        </div>

        <h2>Choix des participants</h2>
        <p>
            Choisissez les utilisateurs qui pourront rédiger des propositions et les utilisateurs qui pourront voter
        </p>
    </div>

    <div id="poser-question__body">

        <div id="roles_input">
            <div id="redacteurs_input">
                <label>Rédacteurs : </label>
                <button type="button" id="add_redacteur">+</button>
            </div>
            <div id="votants_input">
                <label>Votants : </label>
                <button type="button" id="add_votant">+</button>
            </div>
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
    <input type="hidden" name="systemeVote" value="<?= $systemeVote ?>">

    <input type="hidden" name="dateDebutRedaction" value="<?= $dateDebutRedaction ?>">
    <input type="hidden" name="heureDebutRedaction" value="<?= $heureDebutRedaction ?>">
    <input type="hidden" name="dateFinRedaction" value="<?= $dateFinRedaction ?>">
    <input type="hidden" name="heureFinRedaction" value="<?= $heureFinRedaction ?>">
    <input type="hidden" name="dateOuvertureVotes" value="<?= $dateOuvertureVotes ?>">
    <input type="hidden" name="heureOuvertureVotes" value="<?= $heureOuvertureVotes ?>">
    <input type="hidden" name="dateFermetureVotes" value="<?= $dateFermetureVotes ?>">
    <input type="hidden" name="heureFermetureVotes" value="<?= $heureFermetureVotes ?>">

    <div id="poser-question__bottom">
        <button type="submit">
            Poser la question
        </button>
    </div>
</form>


<script>
    const redacteurs = <?= json_encode($redacteurs) ?>;
    const votants = <?= json_encode($votants) ?>;
    const utilisateurs = <?= json_encode($utilisateurs) ?>;
    const allUtilisateurOptions = '<option value="" selected disabled>---<\/option>' + utilisateurs.map(utilisateur => `<option value="${utilisateur.username}">${utilisateur.nomUsuel}<\/option>`).join("\n");
</script>
<script type='module' src="js/formulairePoserQuestion_roles.js"></script>