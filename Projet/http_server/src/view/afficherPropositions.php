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
$nomResponsable = $responsable->getPrenom() . " " . strtoupper($responsable->getNom());

$estResponsable = $responsable->getUsername() === $username;
$estOrganisateur = $question->getUsernameOrganisateur() === $username;
?>

<div class="panel" id="afficherPropositions">
    <input type="checkbox" id="questionDescription" class="texteDepliantTrigger" />
    <div id="propositionTitle">
        <h1 class="title"><?= $question->getTitre() ?></h1>
        <label for="questionDescription">
            <img src='./assets/images/arrow.svg' class='arrow' alt='open and close arrow' />
        </label>
    </div>
    <div id="description"><?= \App\SAE\Lib\Markdown::toHtml($question->getDescription()) ?></div>

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
            echo "<span>$contenu</span>";
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
                <a class="button" href="#modalSupprimer">Supprimer</a>
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
        <p>Le vote se déroule en 1 tour. Choisissez une unique proposition parmi les suivantes</p>
        <div>
            <?php

            foreach ($propositions as $p) {
                $idProposition = rawurlencode($p->getIdProposition());
                $titreProposition = htmlspecialchars($p->getTitreProposition());

                $html = <<<HTML
                    <div for="choix$idProposition">
                        <label>$titreProposition</label>
                        <input type="radio" name="idProposition" id="choix$idProposition" value="$idProposition">
                    </div>
                HTML;

                echo $html;
            }
            ?>
        </div>
        <input type="submit" value="Voter">
    </form>








</div>