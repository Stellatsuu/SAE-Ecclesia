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

echo '<pre>';
print_r($dataQuestion);
echo '</pre>';

?>

<form method="post" action="frontController.php?controller=question&action=poserQuestion" class="panel">
    <h1>Posez votre question :</h1>

    <div id="poser-question__top">
        <div id="progress-bar">
            <span class="progress-bar__step filling"></span>
            <span class="progress-bar__step"></span>
            <span class="progress-bar__step"></span>
            <span class="progress-bar__step"></span>
            <span class="progress-bar__step"></span>
        </div>

        <h2>Confirmation</h2>
    </div>

    <div id="poser-question__body">

    </div>

    <input type="hidden" name="idQuestion" value="<?= $idQuestion ?>">

    <div id="poser-question__bottom">
        <button type="submit">
            Poser la question
        </button>
    </div>
</form>