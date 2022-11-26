<?php

use App\SAE\Model\DataObject\Question;
use App\SAE\Lib\PhaseQuestion as Phase;

$i = 0;
?>
<div class="panel">
    <h1>Mes Questions</h1>

    <?php
    foreach ($questions as $q) {
        $i++;
        $q = Question::toQuestion($q);

        $titre = htmlspecialchars($q->getTitre());
        $description = htmlspecialchars($q->getDescription());
        $idQuestion = rawurlencode($q->getIdQuestion());

        $phase = $q->getPhase();
        $phaseStr = htmlspecialchars($phase->toString());
        $nextPhaseStr = "";

        $openModalBoutonTemplate = <<<HTML
            <a href="#modal$i" class="button">Phase suivante</a>
        HTML;

        $editerBoutonTemplate = <<<HTML
            <a class="button" href="frontController.php?controller=question&action=afficherFormulairePoserQuestion&idQuestion=$idQuestion">Éditer</a>
        HTML;

        $passageRedactionBoutonTemplate = <<<HTML
            <a class="button validerBtn" href="frontController.php?controller=question&action=passagePhaseRedaction&idQuestion=$idQuestion">Oui</a>
        HTML;

        $passageVoteBoutonTemplate = <<<HTML
            <a class="button validerBtn" href="frontController.php?controller=question&action=passagePhaseVote&idQuestion=$idQuestion">Oui</a>
        HTML;

        switch ($phase) {
            case Phase::NonRemplie:
                $openModalBouton = "";
                $nextPhaseBouton = "";
                $nextPhaseStr = "";
                $editerBouton = $editerBoutonTemplate;
                break;
            case Phase::Attente:
                $openModalBouton = $openModalBoutonTemplate;
                $nextPhaseBouton = $passageRedactionBoutonTemplate;
                $nextPhaseStr = "rédaction";
                $editerBouton = $editerBoutonTemplate;
                break;
            case Phase::Redaction:
                $openModalBouton = $openModalBoutonTemplate;
                $nextPhaseBouton = $passageVoteBoutonTemplate;
                $nextPhaseStr = "vote";
                $editerBouton = "";
                break;
            case Phase::Lecture:
                $openModalBouton = "";
                $nextPhaseBouton = $passageVoteBoutonTemplate;
                $nextPhaseStr = "vote";
                $editerBouton = "";
                break;
            case Phase::Vote:
                $openModalBouton = "";
                $nextPhaseBouton = "";
                $nextPhaseStr = "";
                $editerBouton = "";
                break;
            case Phase::Resultat:
                $openModalBouton = "";
                $nextPhaseBouton = "";
                $nextPhaseStr = "";
                $editerBouton = "";
                break;
        }

        $html = <<<HTML
        <div class="question">
            <div class="boite">
                <div>
                    <h2>$titre</h2>
                    <p>$description</p>
                </div>
                <div>
                    <p>Phase : $phaseStr</p>
                    $openModalBouton
                    $editerBouton
                </div>
            </div>
        </div>
        HTML;

        $modalHtml = <<<HTML
        <div id="modal$i" class="modal">
            <div class="modal-content boite">
                <p>Êtes-vous sûr(e) de vouloir passer à la phase de $nextPhaseStr ?</p>
                <div>
                    <a class="button refuserBtn" href="#">Non</a>
                    $nextPhaseBouton
                </div>
                <a href="#" class="modal-close">X</a>
            </div>
         </div>
        HTML;

        echo $html;
        if ($nextPhaseBouton != "") {
            echo $modalHtml;
        }
    }
    ?>

</div>