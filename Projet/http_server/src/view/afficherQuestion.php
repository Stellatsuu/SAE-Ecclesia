<?php

use App\SAE\Lib\Markdown;
use App\SAE\Lib\PhaseQuestion as Phase;
use App\SAE\Lib\PhotoProfil;

//$dataQuestion
$idQuestion = rawurlencode($dataQuestion['idQuestion']);
$titre = htmlspecialchars($dataQuestion['titre']);
$description = Markdown::toHtml($dataQuestion['description']);
$tags = $dataQuestion['tags'];
$nomUsuelOrga = htmlspecialchars($dataQuestion['nomUsuelOrga']);
$nomSystemeVote = htmlspecialchars($dataQuestion['nomSystemeVote']);
$phase = $dataQuestion['phase'];
$sections = $dataQuestion['sections'];
$propositions = $dataQuestion['propositions'];

//$peutEditer
//$peutChangerPhase
//$peutEcrireProposition
//$peutVoter

$sectionHTMLs = array_map(function ($section) {
    $titre = htmlspecialchars($section['titre']);
    $description = Markdown::toHtml($section['description']);

    return <<<HTML
    <details>
        <summary class="titre-section">$titre</summary>
        <div class='description-section markdown'>$description</div>
    </details>
    HTML;
}, $sections);

$propositionHTMLs = array_map(function ($proposition) use ($idQuestion) {
    $idProposition = rawurlencode($proposition['idProposition']);
    $titre = htmlspecialchars($proposition['titre']);
    $nomUsuelResp = htmlspecialchars($proposition['nomUsuelResp']);
    $estAVous = $proposition['estAVous'];
    $pfp = PhotoProfil::getBaliseImg($proposition['pfp'], "photo de profil", $estAVous ? "pfp--self" : "");
    $nomUsuelResp = $estAVous ? "<strong>Vous</strong>" : htmlspecialchars($proposition['nomUsuelResp']);

    return <<<HTML
    <div class="proposition-compact">
            <div class="proposition-compact__pfp user-tooltip">
                $pfp
                <div class="user-tooltip__text">
                    $nomUsuelResp
                </div>
            </div>
            <a href="frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestion&idProposition=$idProposition">
                $titre
            </a>
    </div>
    HTML;
}, $propositions);

$ligneExplicationSysVote = $phase == Phase::Resultat ?
    "Le choix s'est fait par un $nomSystemeVote." :
    "Le choix se fera par un $nomSystemeVote.";

$lienVoirResultats = "<a href='frontController.php?controller=question&action=afficherResultats&idQuestion=$idQuestion'>Voir les résultats</a>";
$lienVoirPropositions = "<a href='frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestion'>Lire les propositions</a>";
$lienVoterPropositions = "<a href='frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestion'>Voter pour une proposition</a>";

if ($peutVoter) {
    $ligneExplicationSysVote .= " $lienVoterPropositions.";
} else if ($phase == Phase::Resultat) {
    $ligneExplicationSysVote .= " $lienVoirResultats. ";
} else {
    $ligneExplicationSysVote .= " $lienVoirPropositions. ";
}
?>

<div id="afficher-question" class="panel">
    <div id="afficher-question__top">
        <h1><?= $titre ?>
            <span>
                par&nbsp;<?= $nomUsuelOrga ?>
            </span>
        </h1>

        <div id="afficher-question__top__actions">
            <?php
            if ($peutEditer)
                echo <<<HTML
                        <a class="button" href="frontController.php?controller=question&action=afficherFormulairePoserQuestion&idQuestion=$idQuestion">Éditer</a>
                    HTML;

            if ($peutChangerPhase)
                echo <<<HTML
                        <a class="button modal-open" href="#modal-phase-suivante">Phase suivante</a>
                    HTML;
            ?>
        </div>
    </div>

    <div id="afficher-question__description" class="panel2">
        <h2>Description :</h2>
        <div id="description" class="markdown"><?= $description ?></div>
    </div>

    <div id="afficher-question__tags" class="panel2">
        <h2>Tags :</h2>

        <div id="tags_list">
            <?php
            if ($tags == [])
                echo "Aucun tag n'a encore été ajouté.";
            else
                foreach ($tags as $tag) {
                    $tag = htmlspecialchars($tag);
                    echo <<<HTML
                        <div class="tag">
                            <span>
                                $tag
                            </span>
                        </div>
                    HTML;
                }
            ?>
        </div>
    </div>


    <div id="afficher-question__sections" class="panel2">
        <h2>Sections :</h2>

        <?php
        if ($sectionHTMLs == [])
            echo "Aucune section n'a encore été écrite.";
        echo implode('', $sectionHTMLs);
        ?>

    </div>

    <div id="afficher-question__calendrier" class="panel2">
        <h2>Calendrier :</h2>

        <h1>TODO!</h1>
    </div>

    <div class="panel2">

        <h2>Propositions :</h2>

        <?php
        if ($phase != Phase::NonRemplie)
            echo <<<HTML
                <p id="afficher-question__systeme-vote">$ligneExplicationSysVote</p>
            HTML;
        ?>

        <div id="afficher-question__propositions">

            <?php
            if ($propositionHTMLs == [])
                echo "Aucune proposition n'a encore été écrite.";
            echo implode('', $propositionHTMLs);
            ?>

        </div>

        <?php
        $boutonEcrireProposition = <<<HTML
            <a class="button" href="frontController.php?controller=proposition&action=afficherFormulaireEcrireProposition&idQuestion=$idQuestion">Ecrire une proposition</a>
        HTML;

        if ($peutEcrireProposition)
            echo $boutonEcrireProposition;
        ?>
    </div>

    <?php
    $passageRedactionBoutonTemplate = <<<HTML
        <a class="button validerBtn" href="frontController.php?controller=question&action=passagePhaseRedaction&idQuestion=$idQuestion">Oui</a>
    HTML;

    $passageVoteBoutonTemplate = <<<HTML
        <a class="button validerBtn" href="frontController.php?controller=question&action=passagePhaseVote&idQuestion=$idQuestion">Oui</a>
    HTML;

    $passageResultatBoutonTemplate = <<<HTML
        <a class="button validerBtn" href="frontController.php?controller=question&action=passagePhaseResultats&idQuestion=$idQuestion">Oui</a>
    HTML;

    switch ($phase) {
        case Phase::Attente:
            $messageConfirmation = "Êtes vous sûr(e) de vouloir passer à la phase de rédaction ?";
            $nextPhaseBouton = $passageRedactionBoutonTemplate;
            break;
        case Phase::Redaction:
            $messageConfirmation = "Êtes vous sûr(e) de vouloir passer à la phase de vote ?";
            $nextPhaseBouton = $passageVoteBoutonTemplate;
            break;
        case Phase::Vote:
            $messageConfirmation = "Êtes vous sûr(e) de vouloir clore la question ?";
            $nextPhaseBouton = $passageResultatBoutonTemplate;
            break;
        default:
            $messageConfirmation = "";
            $nextPhaseBouton = "";
    }
    ?>

    <div id="modal-phase-suivante" class="modal">
        <div class="modal-content panel">
            <p><?= $messageConfirmation ?></p>
            <div>
                <a class="button refuserBtn" href="#">Non</a>

                <?= $nextPhaseBouton ?>
            </div>
            <a href="#" class="modal-close">
                <img src="assets/images/close-icon.svg" alt="bouton fermer">
            </a>
        </div>
    </div>

</div>