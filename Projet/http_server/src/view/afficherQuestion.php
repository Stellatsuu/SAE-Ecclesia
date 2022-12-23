<?php

use App\SAE\Lib\Markdown;
use App\SAE\Lib\PhaseQuestion;
use App\SAE\Lib\PhotoProfil;
use App\SAE\Model\DataObject\Proposition;
use App\SAE\Model\DataObject\Question;
use App\SAE\Model\Repository\PropositionRepository;

$question = Question::castIfNotNull($question);
$idQuestion = $question->getIdQuestion();
$titreQuestion = $question->getTitre();
$descriptionQuestion = $question->getDescription();
$descriptionQuestion_md = Markdown::toHtml($descriptionQuestion);
$sections = $question->getSections();
$organisateur = $question->getOrganisateur();


$sectionHTMLs = [];
for ($i = 0; $i < count($sections); $i++) {
    $nomSection = $sections[$i]->getNomSection();
    $descriptionSection = $sections[$i]->getDescriptionSection();
    $descriptionSection_md = Markdown::toHtml($descriptionSection);

    $sectionHTML = <<<HTML
    <details>
        <summary class="titre-section">$nomSection</summary>
        <span class='description-section markdown'>$descriptionSection_md</span>
    </details>
    HTML;

    $sectionHTMLs[] = $sectionHTML;
}

$propositionHTMLs = [];
for ($i = 0; $i < count($propositions); $i++) {
    $proposition = Proposition::castIfNotNull($propositions[$i]);
    $titreProposition = $proposition->getTitreProposition();
    $b64img = $proposition->getResponsable()->getPhotoProfil(64);
    $pfp = PhotoProfil::getBaliseImg($b64img, "photo de profil");

    $propositionHTML = <<<HTML
        <div class="proposition-compact">
                <span class="proposition-compact__pfp user-tooltip">
                    $pfp
                    <div class="user-tooltip__text">
                        {$proposition->getResponsable()->getNomUsuel()}
                    </div>
                </span>
                <a href="frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestion&index=$i">
                    $titreProposition
                </a>
        </div>
    HTML;

    $propositionHTMLs[] = $propositionHTML;
}

?>

<div id="afficher-question" class="panel">
    <div id="afficher-question__top">
        <h1><?= $titreQuestion ?>
            <span>
                par&nbsp;<?= $organisateur->getNomUsuel() ?>
            </span>
        </h1>
    </div>

    <div id="afficher-question__description" class="panel2">
        <h2>Description :</h2>
        <span id="description" class="markdown"><?= $descriptionQuestion_md ?></span>
    </div>


    <div id="afficher-question__sections" class="panel2">
        <h2>Sections :</h2>

        <?= implode('', $sectionHTMLs) ?>
    </div>


    <div class="panel2">

        <h2>Propositions :</h2>

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