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


    if (ConnexionUtilisateur::estConnecte() && ConnexionUtilisateur::getUsername() == $utilisateur->getUsername()) {
        $nomUsuel = "<strong>Vous</strong>";
        $imgClass = "pfp--self";
    } else {
        $imgClass = "";
    }

    $pfp = PhotoProfil::getBaliseImg($b64img, "photo de profil", $imgClass);

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
    } elseif ($isCurrent) {
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
} elseif ($page == 1) {
    $paginationLinks = [
        pageLink(-1, "&lt&lt", $nbPages, $query, $filtres, false),
        pageLink(-1, "&lt", $nbPages, $query, $filtres, false),
        pageLink(1, "1", $nbPages, $query, $filtres, true, true),
        pageLink(2, "2", $nbPages, $query, $filtres),
        pageLink(3, "3", $nbPages, $query, $filtres),
        pageLink(2, "&gt", $nbPages, $query, $filtres),
        pageLink($nbPages, "&gt&gt", $nbPages, $query, $filtres)
    ];
} elseif ($page >= $nbPages) {
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

$modeMesQuestions = false;
$boutonAutrePage = "";
$filtreMesQuestions = "";
$titrePage = "Questions";
if (ConnexionUtilisateur::estConnecte()) {
    $modeMesQuestions = isset($_GET["f_mq"]);
    $lienAutrePage = $modeMesQuestions ? "frontController.php?controller=question&action=listerQuestions" : "frontController.php?controller=question&action=listerQuestions&f_mq=true";
    $style= $modeMesQuestions ? "" : "style='transform: rotate(180deg);'";
    $boutonAutrePage = <<<HTML
        <span class="switch-mode-container">
            <span>
                Toutes les questions
            </span>
            <a href="$lienAutrePage" class="switch-mode">
                <img src="assets/images/switch-icon.svg" alt="switch" $style>
            </a>
            <span>
                Mes questions
            </span>
        </span>
    HTML;

    if ($modeMesQuestions) {
        $filtreMesQuestions = <<<HTML
            <input type="hidden" name="f_mq" value="true">
        HTML;
        $titrePage = "Mes questions";
    }

}

$filtresRolesHTMLElements = [];
if (ConnexionUtilisateur::estConnecte()) {
    $filtresRolesHTMLElements[] = <<<HTML
        <span class="menu-filtre-4fr">Rôles(s)</span>
    HTML;
    foreach (["coauteur" => "Co-auteur", "redacteur" => "Rédacteur", "votant" => "Votant"] as $role => $titre) {
        $checked = isset($_GET["f_$role"]) ? "checked" : "";
        $filtresRolesHTMLElements[] = <<<HTML
            <input type="checkbox" id="f_{$role}_id" name="f_$role" value="true" $checked>
            <label for="f_{$role}_id">$titre</label>
        HTML;
    }
}

$filtresPhasesHTMLElements = [];
$filtresPhasesHTMLElements[] = <<<HTML
    <span class="menu-filtre-4fr">Phase(s)</span>
HTML;

if ($modeMesQuestions) {
    $phases = [
        "non_remplie" => "Non remplie",
        "attente" => "En attente",
        "lecture" => "Lecture",
        "redaction" => "Rédaction",
        "vote" => "Vote",
        "resultat" => "Résultat"
    ];
} else {
    $phases = [
        "lecture" => "Lecture",
        "redaction" => "Rédaction",
        "vote" => "Vote",
        "resultat" => "Résultat"
    ];
}

foreach ($phases as $phase => $titre) {
    $checked = isset($_GET["f_$phase"]) ? "checked" : "";
    $filtresPhasesHTMLElements[] = <<<HTML
        <input type="checkbox" id="f_{$phase}_id" name="f_$phase" value="true" $checked>
        <label for="f_{$phase}_id">$titre</label>
    HTML;
}

?>

<div class="panel" id="liste-questions">

    <div id="liste-questions__top">
        <h1><?= $titrePage ?></h1>

        <?= $boutonAutrePage ?>

        <div class="barre-recherche">

            <form action="frontController.php" method="get">
                <input type="hidden" name="controller" value="question">
                <input type="hidden" name="action" value="listerQuestions">

                <div class="menu-filtre">
                    <a class="bouton-ouvrir-filtres" href="#"><img src="assets/images/filter-icon.svg" alt="bouton filtres"></a>

                    <div class="filtres">
                        <a id="lien-tout-supprimer" href="frontController.php?controller=question&action=listerQuestions">Tout supprimer</a>

                        <?= $filtreMesQuestions ?>
                        <?= implode("", $filtresPhasesHTMLElements) ?>
                        <?= implode("", $filtresRolesHTMLElements) ?>

                        <input type="submit" value="Valider" class="button">
                    </div>
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