<?php

use App\SAE\Lib\Markdown;
use App\SAE\Lib\PhaseQuestion;

//$dataQuestion
$idQuestion = rawurlencode($dataQuestion['idQuestion']);
$titreQuestion = htmlspecialchars($dataQuestion['titreQuestion']);
$descriptionQuestion = Markdown::toHtml($dataQuestion['descriptionQuestion']);
$nomUsuelOrganisateur = htmlspecialchars($dataQuestion['nomUsuelOrganisateur']);
$interfaceVote = $dataQuestion['interfaceVote'];

//$dataProposition
$idProposition = rawurlencode($dataProposition['idProposition']);
$titreProposition = htmlspecialchars($dataProposition['titreProposition']);
$nomUsuelResponsable = htmlspecialchars($dataProposition['nomUsuelResponsable']);
$paragraphes = $dataProposition['paragraphes'];

//$index
//$nbPropositions

//$peutSupprimer
//$peutEditer
//$peutVoter
//$peutGererCoAuteurs
//$peutDemanderCoAuteur

if ($nbPropositions == 0) {

    $bodyContent = <<<HTML
    <div class="panel2">

        <img src="assets/images/anime.gif" alt="sad anime boy gif">

        <h2>Il n'y a aucune proposition pour cette question</h2>
    </div>
    HTML;

    $formulaireVoteHTML = "";
} else {

    $indexPrevious = $index == 0 ? $nbPropositions - 1 : $index - 1;
    $indexNext = ($index + 1) % $nbPropositions;

    $selectorHTML = <<<HTML
    <div id="afficher-propositions__selector">
        <a href="frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestion&index=$indexPrevious">
            <img src="assets/images/arrow.svg" style="transform: rotate(90deg)" alt="flèche vers la gauche">
        </a>
        <span class="boite">$titreProposition</span>
        <a href="frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestion&index=$indexNext">
            <img src="assets/images/arrow.svg" style="transform: rotate(-90deg)" alt="flèche vers la droite">
        </a>
    </div>
    HTML;


    $paragraphesHTML = array_map(function ($paragraphe) {
        $titre = htmlspecialchars($paragraphe['titre']);
        $contenu = Markdown::toHtml($paragraphe['contenu']);

        return <<<HTML
            <h2>$titre</h2>
            <div class = "markdown">$contenu</div>
        HTML;
    }, $paragraphes);
    $paragraphesHTML = implode("", $paragraphesHTML);

    $modalHtmlProp = <<<HTML
        <div id="modalSupprimerProp" class="modal">
            <div class="modal-content panel">
                <p>Êtes-vous sûr(e) de vouloir supprimer la proposition ?</p>
                <div>
                    <a class="button refuserBtn" href="#">Non</a>
                    <a class="button validerBtn" href="frontController.php?controller=proposition&action=supprimerProposition&idProposition=$idProposition">Oui</a>
                </div>
                <a href="#" class="modal-close">
                    <img src="assets/images/close-icon.svg" alt="bouton fermer">
                </a>
            </div>
         </div>
    HTML;

    $modalHtmlVote = <<<HTML
        <div id="modalSupprimerVote" class="modal">
            <div class="modal-content panel">
                <p>Êtes-vous sûr(e) de vouloir supprimer votre vote ?</p>
                <div>
                    <a class="button refuserBtn" href="#">Non</a>
                    <a class="button validerBtn" href="frontController.php?controller=vote&action=supprimerVote&idQuestion=$idQuestion">Oui</a>
                </div>
                <a href="#" class="modal-close">
                    <img src="assets/images/close-icon.svg" alt="bouton fermer">
                </a>
            </div>
         </div>
    HTML;

    $btnSupprimerVoteHTML = <<<HTML
        <a class="button modal-open"  href="#modalSupprimerVote">Supprimer mon vote</a>
    HTML;

    $btnSupprimerPropHTML = <<<HTML
        <a class="button modal-open"  href="#modalSupprimerProp">Supprimer ma proposition</a>
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

    $btnSupprimerPropHTML = $peutSupprimer ? $btnSupprimerPropHTML : "";
    $btnSupprimerVoteHTML = $aDejaVote ? $btnSupprimerVoteHTML : "";
    $btnModifierHTML = $peutEditer ? $btnModifierHTML : "";
    $btnDemanderCoAuteurHTML = $peutDemanderCoAuteur ? $btnDemanderCoAuteurHTML : "";
    $btnGererCoAuteursHTML = $peutGererCoAuteurs ? $btnGererCoAuteursHTML : "";

    $bodyContent = <<<HTML
        $selectorHTML

        <div class="boite" id="afficher-propositions__proposition">
            $paragraphesHTML
        </div>

        <div id="afficher-propositions__proposition__under">
            <p id="afficher-propositions__nom-responsable">
                $nomUsuelResponsable
            </p>
            
            <div id="afficher-propositions__boutons">
                $btnDemanderCoAuteurHTML
                $btnModifierHTML
                $btnSupprimerPropHTML
                $btnGererCoAuteursHTML
            </div>
        </div>
    HTML;

    $formulaireVoteHTML = <<<HTML
        <form id="afficher-propositions__formulaire-vote" action="frontController.php?controller=vote&action=voter" method="post">
            <h2>Vote</h2>
            $btnSupprimerVoteHTML
            <input type="hidden" name="idQuestion" value="$idQuestion">
            $interfaceVote
        </form>
    HTML;

    $formulaireVoteHTML = $peutVoter ? $formulaireVoteHTML : "";
}
?>

<div class="panel" id="afficher-propositions">

    <div id="afficher-propositions__top">
        <h1><?= $titreQuestion ?>
            <span>
                par&nbsp;<?= $nomUsuelOrganisateur ?>
            </span>
        </h1>
    </div>

    <div id="afficher-propositions__description" class="panel2">
        <h2>Description :</h2>
        <div id="description" class="markdown"><?= $descriptionQuestion ?></div>
    </div>

    <div id="afficher-propositions__body">
        <?= $bodyContent ?>
    </div>

    <div id="afficher-propositions__bottom">
        <?= $formulaireVoteHTML ?>
    </div>

</div>

<?= $modalHtmlProp ?>
<?= $modalHtmlVote ?>
