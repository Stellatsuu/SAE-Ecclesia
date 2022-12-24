<?php

use App\SAE\Lib\ConnexionUtilisateur;
use App\SAE\Lib\Markdown;
use App\SAE\Lib\PhaseQuestion;
use App\SAE\Lib\PhotoProfil;
use App\SAE\Model\DataObject\Proposition;
use App\SAE\Model\DataObject\Question;
use App\SAE\Model\Repository\PropositionRepository;
use App\SAE\Model\Repository\VotantRepository;

$username = ConnexionUtilisateur::getUsername() ?? '';
$question = Question::castIfNotNull($question);
$idQuestion = $question->getIdQuestion();
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

    $pfp = PhotoProfil::getBaliseImg($b64img, "photo de profil");

    $propositionHTML = <<<HTML
        <div class="proposition-compact">
                <span class="proposition-compact__pfp user-tooltip">
                    $pfp
                    <div class="user-tooltip__text">
                        $nomUsuelResp
                    </div>
                </span>
                <a href="frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestion&index=$i">
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

$lienVoirResultats = "<a href='frontController.php?controller=question&action=afficherResultats&idQuestion=$idQuestion'>Voir les résultats</a>";
$lienVoirPropositions = "<a href='frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestion'>Lire les propositions</a>";
$lienVoterPropositions = "<a href='frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestion'>Voter pour une proposition</a>";

if($phase == PhaseQuestion::Vote && $estVotant) {
   $ligneExplicationSysVote .= " $lienVoterPropositions";
} else if($phase == PhaseQuestion::Resultat) {
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
    </div>

    <div id="afficher-question__description" class="panel2">
        <h2>Description :</h2>
        <span id="description" class="markdown"><?= $descriptionQuestion ?></span>
    </div>


    <div id="afficher-question__sections" class="panel2">
        <h2>Sections :</h2>

        <?= implode('', $sectionHTMLs) ?>
    </div>


    <div class="panel2">

        <h2>Propositions :</h2>
        <p id="afficher-question__systeme-vote">
            <?= $ligneExplicationSysVote ?>
        </p>

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
        <a class="button" href="frontController.php?controller=proposition&action=afficherFormulaireEcrireProposition&idQuestion=$idQuestion">Ecrire une proposition</a>
        HTML;

        if ($peutEcrireProposition) {
            echo $boutonEcrireProposition;
        }
        ?>

    </div>













</div>