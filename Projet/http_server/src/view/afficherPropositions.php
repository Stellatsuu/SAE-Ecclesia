<?php

use App\SAE\Lib\ConnexionUtilisateur;
use App\SAE\Lib\Markdown;
use App\SAE\Lib\PhaseQuestion;
use App\SAE\Model\DataObject\Paragraphe;
use App\SAE\Model\DataObject\Proposition;
use App\SAE\Model\DataObject\Question;
use App\SAE\Model\Repository\CoAuteurRepository;
use App\SAE\Model\Repository\PropositionRepository;
use App\SAE\Model\Repository\VotantRepository;

$username = ConnexionUtilisateur::getUsername() ?? "";
$question = Question::castIfNotNull($question);
$phase = $question->getPhase();
$idQuestion = $question->getIdQuestion();
$organisateur = $question->getOrganisateur();
$usernameOrganisateur = $organisateur->getUsername();
$systemeVote = $question->getSystemeVote();
$interfaceVote = $systemeVote->afficherInterfaceVote();
//$propositions passé par le Controller
//$index passé par le Controller

$titreQuestion = htmlspecialchars($question->getTitre());
$descriptionQuestion = Markdown::toHtml($question->getDescription());
$nomUsuelOrga = htmlspecialchars($organisateur->getNomUsuel());

if (count($propositions) == 0) {

    $bodyContent = <<<HTML
    <div class="panel2">
        <h2>Il n'y a aucune proposition pour cette question</h2>
    </div>
    HTML;

    $formulaireVoteHTML = "";

} else {
    $index = $index % count($propositions);
    $nbPropopositions = count($propositions);
    $indexPrevious = $index == 0 ? $nbPropopositions - 1 : $index - 1;
    $indexNext = ($index + 1) % $nbPropopositions;

    $propositionActuelle = Proposition::castIfNotNull($propositions[$index]);
    $idProposition = $propositionActuelle->getIdProposition();
    $usernameResponsable = $propositionActuelle->getUsernameResponsable();
    $paragraphes = $propositionActuelle->getParagraphes();

    $nomUsuelResp = htmlspecialchars($propositionActuelle->getResponsable()->getNomUsuel());
    $titreProposition = htmlspecialchars($propositionActuelle->getTitreProposition());

    $paragraphesHTML = [];
    foreach ($paragraphes as $paragraphe) {
        $nomSection = htmlspecialchars($paragraphe->getSection()->getNomSection());
        $contenu = Markdown::toHtml($paragraphe->getContenuParagraphe());

        $paragrapheHTML = <<<HTML
        <h2>$nomSection</h2>
        <span class = "markdown">$contenu</span>
        HTML;

        $paragraphesHTML[] = $paragrapheHTML;
    }
    $paragraphesHTML = implode("", $paragraphesHTML);

    $estOrganisateur = $username == $usernameOrganisateur;
    $estResponsable = $username == $usernameResponsable;
    $estCoAuteur = (new CoAuteurRepository())->existsForProposition($idProposition, $username) || $estResponsable;
    $estVotant = (new VotantRepository)->existsForQuestion($idQuestion, $username);

    $selectorHTML = <<<HTML
    <div id="afficher-propositions__selector">
        <a href="frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestion&index=$indexPrevious">
            <img src="assets/images/arrow.svg" style="transform: rotate(90deg)">
        </a>
        <span class="boite">$titreProposition</span>
        <a href="frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestion&index=$indexNext">
            <img src="assets/images/arrow.svg" style="transform: rotate(-90deg)">
        </a>
    </div>
    HTML;

    $propositonActuelleHTML = <<<HTML
        <div class="boite" id="afficher-propositions__proposition">
            $paragraphesHTML
        </div>
    HTML;

    $signatureHTML = <<<HTML
        <p id="afficher-propositions__nom-responsable">
            $nomUsuelResp
        </p>
    HTML;

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

    $btnSupprimerHTML = <<<HTML
        <a class="button modal-open"  href="#modalSupprimer">Supprimer</a>
    HTML;

    $btnModifierHTML = <<<HTML
        <a class="button" href="frontController.php?controller=proposition&action=afficherFormulaireContribuerProposition&idProposition=$idProposition">Modifier</a>
    HTML;

    $btnDemanderCoAuteurHTML = <<<HTML
        <a class="button" href="frontController.php?controller=coAuteur&action=afficherFormulaireDemanderCoAuteur&idProposition=$idProposition">Demander à être co-auteur</a>
    HTML;

    $btnGererCoAuteursHTML = <<<HTML
        <a class="button" href="frontController.php?controller=coAuteur&action=afficherFormulaireGererCoAuteurs&idProposition=$idProposition">Gérer les co-auteurs</a>
    HTML;

    $btnSupprimerHTML = ($estOrganisateur || $estResponsable) && ($phase == PhaseQuestion::Redaction || $phase == PhaseQuestion::Lecture) ? $btnSupprimerHTML : "";
    $btnModifierHTML = ($estResponsable || $estCoAuteur) && $phase == PhaseQuestion::Redaction ? $btnModifierHTML : "";
    $btnDemanderCoAuteurHTML = !$estCoAuteur && $phase == PhaseQuestion::Redaction ? $btnDemanderCoAuteurHTML : "";
    $btnGererCoAuteursHTML = $estResponsable && $phase == PhaseQuestion::Redaction ? $btnGererCoAuteursHTML : "";

    $boutonsHTML = <<<HTML
        <div id="afficher-propositions__boutons">
            $btnDemanderCoAuteurHTML
            $btnModifierHTML
            $btnSupprimerHTML
            $btnGererCoAuteursHTML
        </div>
    HTML;

    $bodyContent = <<<HTML
        $selectorHTML
        $propositonActuelleHTML
        <div id="afficher-propositions__proposition__under">
            $signatureHTML
            $boutonsHTML
        </div>
    HTML;

    $formulaireVoteHTML = <<<HTML
        <form id="afficher-propositions__formulaire-vote" action="frontController.php?controller=vote&action=voter" method="post">
            <h2>Vote</h2>
            <input type="hidden" name="idQuestion" value="$idQuestion">
            $interfaceVote
        </form>
    HTML;

    $formulaireVoteHTML = $estVotant && $phase == PhaseQuestion::Vote ? $formulaireVoteHTML : "";
}
?>

<div class="panel" id="afficher-propositions">

    <div id="afficher-propositions__top">
        <h1><?= $titreQuestion ?>
            <span>
                par&nbsp;<?= $nomUsuelOrga ?>
            </span>
        </h1>
    </div>

    <div id="afficher-propositions__description" class="panel2">
        <h2>Description :</h2>
        <span id="description" class="markdown"><?= $descriptionQuestion ?></span>
    </div>

    <div id="afficher-propositions__body">
        <?= $bodyContent ?>
    </div>

    <div id="afficher-propositions__bottom">
        <?= $formulaireVoteHTML ?>
    </div>

</div>