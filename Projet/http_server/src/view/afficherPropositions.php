<?php


use App\SAE\Lib\Markdown;
use App\SAE\Lib\PhaseQuestion;
use App\SAE\Model\DataObject\Proposition;
use App\SAE\Model\DataObject\Question;
use App\SAE\Model\Repository\PropositionRepository;

$propositionActuelle = Proposition::castIfNotNull($propositions[$index]);
$nbPropopositions = count($propositions);
$question = Question::castIfNotNull($question);
$phase = $question->getPhase();
$responsable = $propositionActuelle->getResponsable();
$nomResponsable = $responsable->getNomUsuel();

$estResponsable = $responsable->getUsername() === $username;
$estOrganisateur = $question->getUsernameOrganisateur() === $username;

$systemeVote = $question->getSystemeVote();
$interfaceVote = $systemeVote->afficherInterfaceVote();
?>

<div class="panel" id="afficher-propositions">

    <h1 class="title"><?= $question->getTitre() ?></h1>

    <details class="panel2">
        <summary class="titre-description">Description</summary>
        <span class="description markdown"><?= Markdown::toHtml($question->getDescription()) ?></span>
    </details>

    <div id="propositionSelector">
        <a href="frontController.php?controller=proposition&action=afficherPropositions&idQuestion=<?= rawurlencode($question->getIdQuestion()) ?>&index=<?= $index == 0 ? $nbPropopositions - 1 : $index - 1 ?>">
            <img src="assets/images/arrow.svg" style="transform: rotate(90deg)">
        </a>
        <span class="boite"><?= $propositionActuelle->getTitreProposition(); ?></span>
        <a href="frontController.php?controller=proposition&action=afficherPropositions&idQuestion=<?= rawurlencode($question->getIdQuestion()) ?>&index=<?= ($index + 1) % $nbPropopositions ?>">
            <img src="assets/images/arrow.svg" style="transform: rotate(-90deg)">
        </a>
    </div>

    <div class="boite" id="proposition">
        <?php
        foreach ($propositionActuelle->getParagraphes() as $paragraphe) {
            $nomSection = $paragraphe->getSection()->getNomSection();
            $contenu = Markdown::toHtml($paragraphe->getContenuParagraphe());
            echo "<h2>$nomSection</h2>";
            echo "<span class=\"markdown\">$contenu</span>";
        }
        ?>
    </div>
    <p id="nomResponsable"><?= $nomResponsable ?></p>
    <div id="btns">
        <?php

        $modalHtml = <<<HTML
        <div id="modalSupprimer" class="modal">
            <div class="modal-content boite">
                <p>Êtes vous sûr(e) de vouloir supprimer la proposition ?</p>
                <div>
                    <a class="button refuserBtn" href="#">Non</a>
                    <a class="button validerBtn" href="frontController.php?controller=proposition&action=supprimerProposition&idProposition={$propositionActuelle->getIdProposition()}">Oui</a>
                </div>
                <a href="#" class="modal-close">
                    <img src="assets/images/close-icon.svg" alt="bouton fermer">
                </a>
            </div>
         </div>
        HTML;

        if (($estResponsable || $estOrganisateur) && ($phase == PhaseQuestion::Lecture || $phase == PhaseQuestion::Redaction)) {
            echo <<<HTML
                <a class="button modal-open"  href="#modalSupprimer">Supprimer</a>
            HTML;
            echo $modalHtml;
        }

        if ($estResponsable && $phase == PhaseQuestion::Redaction) {
            echo <<<HTML
            <a class="button" href="frontController.php?controller=proposition&action=afficherFormulaireContribuerProposition&idProposition={$propositionActuelle->getIdProposition()}">Modifier</a>
            HTML;
        }


        ?>
    </div>

    <form id="formulaire_vote" action="frontController.php?controller=vote&action=voter" method="post" style="<?= $phase != PhaseQuestion::Vote ? 'display: none;' : '' ?>">
        <h2>Vote</h2>

        <input type="hidden" name="idQuestion" value="<?= $question->getIdQuestion() ?>">

        <?= $interfaceVote ?>

    </form>

</div>