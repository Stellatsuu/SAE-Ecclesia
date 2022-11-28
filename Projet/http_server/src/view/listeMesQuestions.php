<?php

use App\SAE\Model\DataObject\Question;
use App\SAE\Lib\PhaseQuestion as Phase;

$i = 0;
?>
<div class="panel" id="listeMesQuestions">
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
        $linkPropositions = "";

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

        $passageResultatBoutonTemplate = <<<HTML
            <a class="button validerBtn" href="frontController.php?controller=question&action=passagePhaseResultats&idQuestion=$idQuestion">Oui</a>
        HTML;

        switch ($phase) {
            case Phase::NonRemplie:
                $openModalBouton = "";
                $nextPhaseBouton = "";
                $messageConfirmation = "";
                $editerBouton = $editerBoutonTemplate;
                $linkPropositions = <<<HTML
                <h2>$titre</h2>
                HTML;
                break;
            case Phase::Attente:
                $openModalBouton = $openModalBoutonTemplate;
                $nextPhaseBouton = $passageRedactionBoutonTemplate;
                $messageConfirmation = "Êtes-vous sûr(e) de vouloir passer à la phase de rédaction ?";
                $editerBouton = $editerBoutonTemplate;
                $linkPropositions = <<<HTML
                <h2>$titre</h2>
                HTML;
                break;
            case Phase::Redaction:
                $openModalBouton = $openModalBoutonTemplate;
                $nextPhaseBouton = $passageVoteBoutonTemplate;
                $messageConfirmation = "Êtes-vous sûr(e) de vouloir passer à la phase de vote ?";
                $editerBouton = "";
                $linkPropositions = <<<HTML
                <a href="frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestion&idUtilisateur={$_SESSION['idUtilisateur']}"><h2>$titre</h2></a> 
                HTML;
                break;
            case Phase::Lecture:
                $openModalBouton = $openModalBoutonTemplate;
                $nextPhaseBouton = $passageVoteBoutonTemplate;
                $messageConfirmation = "Êtes-vous sûr(e) de vouloir passer à la phase de vote ?";
                $editerBouton = "";
                $linkPropositions = <<<HTML
                <a href="frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestion&idUtilisateur={$_SESSION['idUtilisateur']}"><h2>$titre</h2></a> 
                HTML;
                break;
            case Phase::Vote:
                $openModalBouton = $openModalBoutonTemplate;
                $nextPhaseBouton = $passageResultatBoutonTemplate;
                $messageConfirmation = "Êtes-vous sûr(e) de vouloir clore la phase de vote ?";
                $editerBouton = "";
                $linkPropositions = <<<HTML
                <a href="frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestion"><h2>$titre</h2></a> 
                HTML;
                break;
            case Phase::Resultat:
                $openModalBouton = "";
                $nextPhaseBouton = "";
                $messageConfirmation = "";
                $editerBouton = "";
                $linkPropositions = <<<HTML
                <h2>$titre</h2>
                HTML;
                break;

        }

        $html = <<<HTML
        <div class="question">
            <div class="boite">
                <div>
                    $linkPropositions
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
                <p>$messageConfirmation</p>
                <div>
                    <a class="button refuserBtn" href="#">Non</a>
                    $nextPhaseBouton
                </div>
                <a href="#" class="modal-close">
                    <img src="assets/images/close-icon.svg" alt="bouton fermer">
                </a>
            </div>
         </div>
        HTML;

        echo $html;
        if ($nextPhaseBouton != "") {
            echo $modalHtml;
        }
    }
    ?>

    <a class=button href="frontController.php?controller=demandeQuestion&action=afficherFormulaireDemandeQuestion">Nouvelle question</a>
</div>