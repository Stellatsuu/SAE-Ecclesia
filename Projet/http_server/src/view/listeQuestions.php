<?php

use App\SAE\Lib\ConnexionUtilisateur;
use App\SAE\Lib\Markdown;
use App\SAE\Lib\PhaseQuestion;
use App\SAE\Lib\PhotoProfil;
use App\SAE\Model\DataObject\Question;

$query = htmlspecialchars(rawurldecode($query));

$questionHTMLs = [];
foreach ($questions as $q) {
    $question = Question::castIfNotNull($q);
    $idQuestion = $question->getIdQuestion();
    $utilisateur = $question->getOrganisateur();

    $query = htmlspecialchars(rawurldecode($query));
    $titre = htmlspecialchars($question->getTitre());
    $description = Markdown::toHtml($question->getDescription());
    $datePublication = "Non publiée";
    if (!($question->getPhase() == PhaseQuestion::NonRemplie || $question->getPhase() == PhaseQuestion::Attente)) {
        $datePublication = htmlspecialchars($question->getDateDebutRedaction()->format("d/m/Y"));
    }
    $phase = htmlspecialchars($question->getPhase()->toString());
    $nomUsuel = htmlspecialchars($utilisateur->getNomUsuel());
    $nomUsuel = preg_replace("/ /", "&nbsp;", $nomUsuel, 1);
    $b64img = htmlspecialchars($utilisateur->getPhotoProfil(64));

    $pfp = PhotoProfil::getBaliseImg($b64img, "photo de profil");

    $html = <<<HTML
        <div class="question-compact">
            <span class="question-compact__top">

                <span class="question-compact__top__pfp user-tooltip">
                    $pfp
                    <div class="user-tooltip__text">
                        $nomUsuel
                    </div>
                </span>

                <a href="frontController.php?controller=question&action=afficherQuestion&idQuestion=$idQuestion">
                    $titre
                </a>
            </span>

            <span class="question-compact__description markdown">
                $description
            </span>

            <span class="question-compact__bottom">
                <span>
                $datePublication
                </span>
                <span>
                    Phase : $phase
                </span>
            </span>
        </div>
    HTML;

    $questionHTMLs[] = $html;
}

function pageLink($page, $text, $nbPages, $query, $filtres, $active = true, $isCurrent = false): string
{
    $filtresString = "";
    foreach ($filtres as $key => $value) {
        $filtresString .= "&f_$value=true";
    }

    if (!$active) {
        $res = <<<html
        <span class="pagination__link pagination__link--inactive unselectable">
            $text
        </span>
        html;
    } else if ($isCurrent) {
        $res = <<<html
        <span class="pagination__link pagination__link--current unselectable">
            $text
        </span>
        html;
    } else {
        $res = <<<html
        <a href="frontController.php?controller=question&action=listerQuestions&page=$page&query=$query$filtresString" class="pagination__link unselectable">
            $text
        </a>
        html;
    }

    if ($page < 1 || $page > $nbPages) {

        if ($page != $text) {
            $res = <<<html
            <span class="pagination__link pagination__link--inactive unselectable">
                $text
            </span>
            html;
        } else {
            $res = "";
        }
    }

    return $res;
}
if ($nbPages == 1) {
    $paginationLinks = [
        pageLink(-1, "&lt&lt", 1, $query, $filtres, false),
        pageLink(-1, "&lt", 1, $query, $filtres, false),
        pageLink(1, "1", 1, $query, $filtres, true, true),
        pageLink(-1, "&gt", 1, $query, $filtres, false),
        pageLink(-1, "&gt&gt", 1, $query, $filtres, false),
    ];
} else if ($page == 1) {
    $paginationLinks = [
        pageLink(-1, "&lt&lt", $nbPages, $query, $filtres, false),
        pageLink(-1, "&lt", $nbPages, $query, $filtres, false),
        pageLink(1, "1", $nbPages, $query, $filtres, true, true),
        pageLink(2, "2", $nbPages, $query, $filtres),
        pageLink(3, "3", $nbPages, $query, $filtres),
        pageLink(2, "&gt", $nbPages, $query, $filtres),
        pageLink($nbPages, "&gt&gt", $nbPages, $query, $filtres)
    ];
} else if ($page >= $nbPages) {
    $paginationLinks = [
        pageLink(1, "&lt&lt", $nbPages, $query, $filtres),
        pageLink($page - 1, "&lt", $nbPages, $query, $filtres),
        pageLink($page - 2, $page - 2, $nbPages, $query, $filtres),
        pageLink($page - 1, $page - 1, $nbPages, $query, $filtres),
        pageLink($page, $page, $nbPages, $query, $filtres, true, true),
        pageLink(-1, "&gt", $nbPages, $query, $filtres, false),
        pageLink(-1, "&gt&gt", $nbPages, $query, $filtres, false)
    ];
} else {
    $paginationLinks = [
        pageLink(1, "&lt&lt", $nbPages, $query, $filtres),
        pageLink($page - 1, "&lt", $nbPages, $query, $filtres),
        pageLink($page - 1, $page - 1, $nbPages, $query, $filtres),
        pageLink($page, $page, $nbPages, $query, $filtres, true, true),
        pageLink($page + 1, $page + 1, $nbPages, $query, $filtres),
        pageLink($page + 1, "&gt", $nbPages, $query, $filtres),
        pageLink($nbPages, "&gt&gt", $nbPages, $query, $filtres)
    ];
}
?>

<div class="panel" id="liste-questions">

    <div id="liste-questions__top">
        <h1>Questions : </h1>

        <div class="barre-recherche">

            <form action="frontController.php" method="get">
                <input type="hidden" name="controller" value="question">
                <input type="hidden" name="action" value="listerQuestions">

                <div class="filtres">
                    <a class="bouton_ouvrir_filtres" href="#"><img src="assets/images/filter-icon.svg" alt="bouton filtres"></a>

                    <span><a href="frontController.php?controller=question&action=listerQuestions">Tout supprimer</a></span>
                    <div class="filtres-phases">
                        <label>Phase(s)</label><br>
                        <input type="checkbox" name="f_lecture" value="true" <?= isset($_GET['f_lecture']) ? "checked" : "" ?>><label>Lecture</label><br>
                        <input type="checkbox" name="f_vote" value="true" <?= isset($_GET['f_vote']) ? "checked" : "" ?>><label>Vote</label><br>
                        <input type="checkbox" name="f_redaction" value="true" <?= isset($_GET['f_redaction']) ? "checked" : "" ?>><label>Rédaction</label><br>
                        <input type="checkbox" name="f_resultat" value="true" <?= isset($_GET['f_resultat']) ? "checked" : "" ?>><label>Résultat</label><br>
                    </div>

                    <div class="filtres-roles">
                        <label>Rôles(s)</label><br>
                        <input type="checkbox" name="f_coauteur" value="true" <?= isset($_GET['f_coauteur']) ? "checked" : "" ?> <?= ConnexionUtilisateur::estConnecte() ? "" : "disabled" ?>><label>Co-auteur</label><br>
                        <input type="checkbox" name="f_redacteur" value="true" <?= isset($_GET['f_redacteur']) ? "checked" : "" ?> <?= ConnexionUtilisateur::estConnecte() ? "" : "disabled" ?>><label>Rédacteur</label><br>
                        <input type="checkbox" name="f_votant" value="true" <?= isset($_GET['f_votant']) ? "checked" : "" ?> <?= ConnexionUtilisateur::estConnecte() ? "" : "disabled" ?>><label>Votant</label><br>
                    </div>

                    <input type="submit" value="Valider" class="button">
                </div>

                <input type="text" name="query" value="<?= $query ?>" />
                <input type="submit" value="" id="validation-search">
            </form>

        </div>

    </div>

    <div id="questions">
        <?php echo implode("", $questionHTMLs); ?>
    </div>

    <div class="pagination">
        <?php echo implode("", $paginationLinks); ?>
    </div>

</div>