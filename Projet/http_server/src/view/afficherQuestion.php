<?php

use App\SAE\Lib\Markdown;
use App\SAE\Lib\PhaseQuestion;
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
    <input type='checkbox' id='deploy_$i' class='texteDepliantTrigger'/>
    <div class='titre-section'>
        <h2>$nomSection</h2>
        <label for='deploy_$i'>
            <img src='./assets/images/arrow.svg' class='arrow' alt='open and close arrow'/>
        </label>
    </div>
    <span class='description-section markdown'>$descriptionSection_md</span>
    HTML;

    $sectionHTMLs[] = $sectionHTML;
}

$propositionHTMLs = [];
for ($i = 0; $i < count($propositions); $i++) {
    $titreProposition = $propositions[$i]->getTitreProposition();

    $propositionHTML = <<<HTML
    <a href="frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestion&index=$i">$titreProposition</a>
    HTML;

    $propositionHTMLs[] = $propositionHTML;
}

?>

<div id="afficher-question" class="panel">
    <div id="titre-question">
        <h1><?= $titreQuestion ?></h1>
        <span>
            par <?= $organisateur->getNomUsuel() ?>
        </span>
    </div>

    <span id="description" class="markdown"><?= $descriptionQuestion_md ?></span>

    <h2>Sections :</h2>

    <div class="afficher-question__sections">
        <?php echo implode('', $sectionHTMLs) ?>
    </div>

    <h2>Propositions :</h2>

    <div class="afficher-question__propositions">
        <?php
        echo implode('', $propositionHTMLs);

        if($propositionHTMLs == []){
            echo "Aucune proposition n'a encore été écrite.";
        }

        $boutonEcrireProposition = <<<HTML
            <a class="button" href="frontController.php?controller=proposition&action=afficherFormulaireEcrireProposition&idQuestion=$idQuestion">Ecrire une proposition</a>
        HTML;

        if ($peutEcrireProposition) {
            echo $boutonEcrireProposition;
        }

        ?>
    </div>











</div>