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
            Choisissez les dates clés du calendrier de la question
        </p>
    </div>

    <div id="poser-question__body">
        <div id="calendrier">
            <div class="calendrierCercleBarre">
                <div class="cercle"></div>
                <div class="barre"></div>
            </div>
            <div class="selecteurDate">
                <h2>Début de la phase de rédaction</h2>
                <div>
                    <span>
                        Le
                        <input required type="date" name="dateDebutRedaction" value="<?= $dateDebutRedaction ?>">
                    </span>
                    <span>
                        à
                        <input required type="time" name="heureDebutRedaction" value="<?= $heureDebutRedaction ?>">
                    </span>
                </div>
                <p>Les rédacteurs rédigent des propositions de réponses à la question.</p>
            </div>

            <div class="calendrierCercleBarre">
                <div class="cercle"></div>
                <div class="barre"></div>
            </div>
            <div class="selecteurDate">
                <h2>Fin de la phase de rédaction</h2>
                <span>
                    Le
                    <input required type="date" name="dateFinRedaction" value="<?= $dateFinRedaction ?>">
                </span>
                <span>
                    à
                    <input required type="time" name="heureFinRedaction" value="<?= $heureFinRedaction ?>">
                </span>
                <p>Les votants peuvent lire les propositions.</p>
            </div>

            <div class="calendrierCercleBarre">
                <div class="cercle"></div>
                <div class="barre"></div>
            </div>
            <div class="selecteurDate">
                <h2>Début de la phase de vote</h2>
                <div>
                    <span>
                        Le
                        <input required type="date" name="dateOuvertureVotes" value="<?= $dateOuvertureVotes ?>">
                    </span>
                    <span>
                        à
                        <input required type="time" name="heureOuvertureVotes" value="<?= $heureOuvertureVotes ?>">
                    </span>
                </div>
                <p>Les votants votent pour la ou les propositions de leur choix.</p>
            </div>

            <div class="calendrierCercleBarre">
                <div class="cercle"></div>
                <div class="barre"></div>
            </div>
            <div class="selecteurDate">
                <h2>Fin de la phase de vote</h2>
                <span>
                    Le
                    <input required type="date" name="dateFermetureVotes" value="<?= $dateFermetureVotes ?>">
                </span>
                <span>
                    à
                    <input required type="time" name="heureFermetureVotes" value="<?= $heureFermetureVotes ?>">
                </span>
                <p>Le résultat du vote est rendu public.</p>
            </div>

            <img src="assets/images/triangle.svg" alt="" id="fleche"></img>
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

    <div id="poser-question__bottom">
        <button type="submit">
            Suivant
        </button>
    </div>
</form>