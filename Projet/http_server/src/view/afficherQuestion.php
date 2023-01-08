<?php

use App\SAE\Lib\ConnexionUtilisateur;
use App\SAE\Lib\Markdown;
use App\SAE\Lib\PhaseQuestion;
use App\SAE\Lib\PhotoProfil;
use App\SAE\Model\DataObject\Proposition;
use App\SAE\Model\DataObject\Question;
use App\SAE\Model\Repository\VotantRepository;

$username = ConnexionUtilisateur::getUsername() ?? '';
$question = Question::castIfNotNull($question);
$idQuestion = $question->getIdQuestion();
$idQuestionUrl = rawurlencode($idQuestion);
$phase = $question->getPhase();
$sections = $question->getSections();
$organisateur = $question->getOrganisateur();
$systemeVote = $question->getSystemeVote();

$systemeVoteNomComplet = htmlspecialchars($systemeVote->getNomComplet());
$titreQuestion = htmlspecialchars($question->getTitre());
$nomUsuelOrga = htmlspecialchars($organisateur->getNomUsuel());
$descriptionQuestion = Markdown::toHtml($question->getDescription());

$sectionHTMLs = [];
for ($i = 0; $i < count($sections); $i++) {

    $nomSection = htmlspecialchars($sections[$i]->getNomSection());
    $descriptionSection = Markdown::toHtml($sections[$i]->getDescriptionSection());

    $sectionHTML = <<<HTML
    <details>
        <summary class="titre-section">$nomSection</summary>
        <span class='description-section markdown'>$descriptionSection</span>
    </details>
    HTML;

    $sectionHTMLs[] = $sectionHTML;
}

$propositionHTMLs = [];
for ($i = 0; $i < count($propositions); $i++) {
    $proposition = Proposition::castIfNotNull($propositions[$i]);
    $responsable = $proposition->getResponsable();

    $titreProposition = htmlspecialchars($proposition->getTitreProposition());
    $b64img = htmlspecialchars($responsable->getPhotoProfil());
    $nomUsuelResp = htmlspecialchars($responsable->getNomUsuel());

    if($responsable->getUsername() == $username) {
        $nomUsuelResp = "<strong>Vous</strong>";
        $pfp = PhotoProfil::getBaliseImg($b64img, "photo de profil", "pfp--self");
    } else {
        $pfp = PhotoProfil::getBaliseImg($b64img, "photo de profil");
    }



    

    $propositionHTML = <<<HTML
        <div class="proposition-compact">
                <span class="proposition-compact__pfp user-tooltip">
                    $pfp
                    <div class="user-tooltip__text">
                        $nomUsuelResp
                    </div>
                </span>
                <a href="frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestionUrl&index=$i">
                    $titreProposition
                </a>
        </div>
    HTML;

    $propositionHTMLs[] = $propositionHTML;
}

$ligneExplicationSysVote = $phase == PhaseQuestion::Resultat ?
    "Le choix s'est fait par un $systemeVoteNomComplet." :
    "Le choix se fera par un $systemeVoteNomComplet.";

$estVotant = (new VotantRepository)->existsForQuestion($idQuestion, $username);

$lienVoirResultats = "<a href='frontController.php?controller=question&action=afficherResultats&idQuestion=$idQuestionUrl'>Voir les résultats</a>";
$lienVoirPropositions = "<a href='frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestionUrl'>Lire les propositions</a>";
$lienVoterPropositions = "<a href='frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestionUrl'>Voter pour une proposition</a>";

if ($phase == PhaseQuestion::Vote && $estVotant) {
    $ligneExplicationSysVote .= " $lienVoterPropositions";
} elseif ($phase == PhaseQuestion::Resultat) {
    $ligneExplicationSysVote .= " $lienVoirResultats";
} else {
    $ligneExplicationSysVote .= " $lienVoirPropositions";
}
?>

<div id="afficher-question" class="panel">
    <div id="afficher-question__top">
        <h1><?= $titreQuestion ?>
            <span>
                par&nbsp;<?= $nomUsuelOrga ?>
            </span>
        </h1>

        <div id="afficher-question__top__actions">
            <?php
            if ($username == $organisateur->getUsername()) {

                if($phase == PhaseQuestion::NonRemplie || $phase == PhaseQuestion::Attente) {
                    echo <<<HTML
                        <a class="button" href="frontController.php?controller=question&action=afficherFormulairePoserQuestion&idQuestion=$idQuestion">Éditer</a>
                    HTML;
                }

                if($phase != PhaseQuestion::Resultat && $phase != PhaseQuestion::NonRemplie) {
                    echo <<<HTML
                        <a class="button modal-open" href="#modal-phase-suivante">Phase suivante</a>
                    HTML;
                }
            }
            ?>
        </div>
    </div>

    <div id="afficher-question__description" class="panel2">
        <h2>Description :</h2>
        <span id="description" class="markdown"><?= $descriptionQuestion ?></span>
    </div>


    <div id="afficher-question__sections" class="panel2">
        <h2>Sections :</h2>

        <?php if ($sectionHTMLs == []) {
            echo "Aucune section n'a encore été écrite.";
        } else {
            echo implode('', $sectionHTMLs);
        }
        ?>
    </div>

    <div id="afficher-question__calendrier" class="panel2">
        <h2>Calendrier :</h2>

        <h1>TODO!</h1>
    </div>

    <div class="panel2">

        <h2>Propositions :</h2>

        <?php

        if ($phase != PhaseQuestion::NonRemplie) {
            echo <<<HTML
            <p id="afficher-question__systeme-vote">
                $ligneExplicationSysVote
            </p>
            HTML;
        }
        ?>

        <div id="afficher-question__propositions">

            <?php
            echo implode('', $propositionHTMLs);

            if ($propositionHTMLs == []) {
                echo "Aucune proposition n'a encore été écrite.";
            }
            ?>
        </div>

        <?php
        $boutonEcrireProposition = <<<HTML
        <a class="button" href="frontController.php?controller=proposition&action=afficherFormulaireEcrireProposition&idQuestion=$idQuestionUrl">Ecrire une proposition</a>
        HTML;

        if ($peutEcrireProposition) {
            echo $boutonEcrireProposition;
        }
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

    switch($phase) {
        case PhaseQuestion::Attente:
            $messageConfirmation = "Êtes vous sûr(e) de vouloir passer à la phase de rédaction ?";
            $nextPhaseBouton = $passageRedactionBoutonTemplate;
            break;
        case PhaseQuestion::Redaction:
            $messageConfirmation = "Êtes vous sûr(e) de vouloir passer à la phase de vote ?";
            $nextPhaseBouton = $passageVoteBoutonTemplate;
            break;
        case PhaseQuestion::Vote:
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