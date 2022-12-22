<?php

use App\SAE\Model\DataObject\Question;

$questionHTMLs = [];
foreach ($questions as $q) {
    $question = Question::castIfNotNull($q);
    $idQuestion = $question->getIdQuestion();
    $titre = $question->getTitre();
    $description = $question->getDescription();
    $datePublication = $question->getDateDebutRedaction()->format("d/m/Y");
    $phase = $question->getPhase()->toString();

    $utilisateur = $question->getOrganisateur();
    $nomUsuel = $utilisateur->getNomUsuel();
    $b64img = $utilisateur->getPhotoProfil(64);
    $pfp = <<<html
    <img src="data:image/png;charset=utf8;base64,$b64img"/>
    html;


    $html = <<<html
        <div class="question-compact">
            <span class="question-compact__top">

                <span class="question-compact__top__pfp">
                    $pfp
                    <div class="question-compact__top__pfp__tooltip">
                        $nomUsuel
                    </div>
                </span>


                <a href="frontController.php?controller=question&action=afficherQuestion&idQuestion=$idQuestion">
                    $titre
                </a>
            </span>

            <p class="question-compact__description">
                $description
            </p>

            <span class="question-compact__bottom">
                <span>
                $datePublication
                </span>
                <span>
                    Phase : $phase
                </span>
            </span>
        </div>
    html;

    $questionHTMLs[] = $html;
}

function pageLink($page, $text, $nbPages, $query, $active = true, $isCurrent = false): string
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
        <a href="frontController.php?controller=question&action=listerQuestions&page=$page&query=$query" class="pagination__link unselectable">
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
        pageLink(-1, "<<", $nbPages, $query, false),
        pageLink(-1, "<", $nbPages, $query, false),
        pageLink(1, "1", $nbPages, $query, true, true),
        pageLink(-1, ">", $nbPages, $query, false),
        pageLink(-1, ">>", $nbPages, $query, false)
    ];
} else if ($page == 1) {
    $paginationLinks = [
        pageLink(-1, "<<", $nbPages, $query, false),
        pageLink(-1, "<", $nbPages, $query, false),
        pageLink(1, "1", $nbPages, $query, true, true),
        pageLink(2, "2", $nbPages, $query),
        pageLink(3, "3", $nbPages, $query),
        pageLink(2, ">", $nbPages, $query),
        pageLink($nbPages, ">>", $nbPages, $query)
    ];
} else if ($page >= $nbPages) {
    $paginationLinks = [
        pageLink(1, "<<", $nbPages, $query),
        pageLink($page - 1, "<", $nbPages, $query),
        pageLink($page - 2, $page - 2, $nbPages, $query),
        pageLink($page - 1, $page - 1, $nbPages, $query),
        pageLink($page, $page, $nbPages, $query, true, true),
        pageLink(-1, ">", $nbPages, $query, false),
        pageLink(-1, ">>", $nbPages, $query, false)
    ];
} else {
    $paginationLinks = [
        pageLink(1, "<<", $nbPages, $query),
        pageLink($page - 1, "<", $nbPages, $query),
        pageLink($page - 1, $page - 1, $nbPages, $query),
        pageLink($page, $page, $nbPages, $query, true, true),
        pageLink($page + 1, $page + 1, $nbPages, $query),
        pageLink($page + 1, ">", $nbPages, $query),
        pageLink($nbPages, ">>", $nbPages, $query)
    ];
}
?>

<div class="panel" id="listeQuestions">
    <h1>Questions : </h1>

    <div class="searchBar">
        <form action="frontController.php" method="get">
            <input type="hidden" name="controller" value="question">
            <input type="hidden" name="action" value="listerQuestions">
            <input type="text" name="query" value="<?= htmlspecialchars(rawurldecode($query)) ?>" />
            <input type="submit" value="Rechercher">
        </form>
    </div>

    <div id="questions">
        <?php echo implode("", $questionHTMLs); ?>
    </div>


    <div class="pagination">
        <?php echo implode("", $paginationLinks); ?>
    </div>

</div>