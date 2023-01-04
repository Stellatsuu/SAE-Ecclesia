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
    if(!($question->getPhase() == PhaseQuestion::NonRemplie || $question->getPhase() == PhaseQuestion::Attente)){
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

function pageLink($page, $text, $nbPages, $query, $filtresQuery, $active = true, $isCurrent = false): string
{
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
        <a href="frontController.php?controller=question&action=listerQuestions&page=$page&query=$query&filtres=$filtresQuery" class="pagination__link unselectable">
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
        pageLink(-1, "<<", 1, $query, $filtresQuery, false),
        pageLink(-1, "<", 1, $query, $filtresQuery,false),
        pageLink(1, "1", 1, $query, $filtresQuery,true, true),
        pageLink(-1, ">", 1, $query, $filtresQuery,false),
        pageLink(-1, ">>", 1, $query, $filtresQuery, false),
    ];
} else if ($page == 1) {
    $paginationLinks = [
        pageLink(-1, "<<", $nbPages, $query, $filtresQuery, false),
        pageLink(-1, "<", $nbPages, $query, $filtresQuery, false),
        pageLink(1, "1", $nbPages, $query, $filtresQuery,true, true),
        pageLink(2, "2", $nbPages, $query, $filtresQuery),
        pageLink(3, "3", $nbPages, $query, $filtresQuery),
        pageLink(2, ">", $nbPages, $query, $filtresQuery),
        pageLink($nbPages, ">>", $nbPages, $query, $filtresQuery)
    ];
} else if ($page >= $nbPages) {
    $paginationLinks = [
        pageLink(1, "<<", $nbPages, $query, $filtresQuery),
        pageLink($page - 1, "<", $nbPages, $query, $filtresQuery),
        pageLink($page - 2, $page - 2, $nbPages, $query, $filtresQuery),
        pageLink($page - 1, $page - 1, $nbPages, $query, $filtresQuery),
        pageLink($page, $page, $nbPages, $query, $filtresQuery,true, true),
        pageLink(-1, ">", $nbPages, $query, $filtresQuery,false),
        pageLink(-1, ">>", $nbPages, $query, $filtresQuery, false)
    ];
} else {
    $paginationLinks = [
        pageLink(1, "<<", $nbPages, $query, $filtresQuery),
        pageLink($page - 1, "<", $nbPages, $query, $filtresQuery),
        pageLink($page - 1, $page - 1, $nbPages, $query, $filtresQuery),
        pageLink($page, $page, $nbPages, $query, $filtresQuery, true, true),
        pageLink($page + 1, $page + 1, $nbPages, $query, $filtresQuery),
        pageLink($page + 1, ">", $nbPages, $query, $filtresQuery),
        pageLink($nbPages, ">>", $nbPages, $query, $filtresQuery)
    ];
}
?>

<div class="panel" id="liste-questions">

    <div id="liste-questions__top">
        <h1>Questions : </h1>
        <div class="barre-recherche">
            <div class="filtres">
                <a class="bouton_ouvrir_filtres" href="#"><img src="assets/images/filter-icon.svg" alt="bouton filtres"></a>
                <form action="frontController.php" method="get">
                    <!-- //TODO préremplissage des cases selon les checkbox precedentes
                         //TODO paginations avec filtres -->
                    <div class="filtres-phases">
                        <label>Phase(s)</label><br>
                        <input type="checkbox" name="lecture" value="lecture"><label>Lecture</label><br>
                        <input type="checkbox" name="vote" value="vote"><label>Vote</label><br>
                        <input type="checkbox" name="redaction" value="redaction"><label>Redaction</label><br>
                        <input type="checkbox" name="resultat" value="resultat"><label>Résultats</label><br>
                    </div>

                    <?php
                    if(ConnexionUtilisateur::estConnecte()){
                        echo <<<html
                        <div class="filtres-roles">
                            <label>Rôles(s)</label><br>
                            <input type="checkbox" name="coauteur" value="coauteur"><label>Co-Auteur</label><br>
                            <input type="checkbox" name="redacteur" value="redacteur"><label>Redacteur</label><br>
                            <input type="checkbox" name="votant" value="votant"><label>Votant</label><br>
                        </div>
                    html;
                    }?>

                    <input type="hidden" name="controller" value="question">
                    <input type="hidden" name="action" value="listerQuestions">
                    <input type="hidden" name="filtresQuery" value="<?= $filtresQuery ?>">
                    <input type="submit" value="Valider" class="button">
                </form>
            </div>
            <form action="frontController.php" method="get">
                <input type="hidden" name="controller" value="question">
                <input type="hidden" name="action" value="listerQuestions">
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