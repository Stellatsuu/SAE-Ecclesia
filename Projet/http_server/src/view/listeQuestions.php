<?php

use App\SAE\Model\DataObject\Question;

$questionHTMLs = [];
foreach ($questions as $q) {
    $question = Question::castIfNotNull($q);
    $idQuestion = $question->getIdQuestion();
    $titre = $question->getTitre();
    $description = $question->getDescription();
    $datePublication = $question->getDateDebutRedaction()->format("d/m/Y");

    $utilisateur = $question->getOrganisateur();
    $b64img = $utilisateur->getPhotoProfil(64);
    $pfp = <<<html
    <img src="data:image/png;charset=utf8;base64,$b64img"/>
    html;

    
    $html = <<<html
        <div class="questionCompact" style="margin-bottom: 1em; padding: 1em; border: 1px solid #ccc;">
            <span class="questionCompact__top">
                $pfp
                <a href="frontController.php?controller=question&action=afficherQuestion&idQuestion=$idQuestion">
                    $titre
                </a>
            </span>

            <p class="questionCompact__description">
                $description
            </p>

            <span class="questionCompact__bottom">
                $datePublication
            </span>
        </div>
    html;

    $questionHTMLs[] = $html;
}

function pageLink($page, $text, $nbPages, $active = true, $isCurrent = false): string
{
    if (!$active) {
        $res = <<<html
        <span class="pagination__link pagination__link--inactive">
            $text
        </span>
        html;
    } else if ($isCurrent) {
        $res = <<<html
        <span class="pagination__link pagination__link--current">
            $text
        </span>
        html;
    } else {
        $res = <<<html
        <a href="frontController.php?controller=question&action=listerQuestions&page=$page" class="pagination__link">
            $text
        </a>
        html;
    }

    if ($page < 1 || $page > $nbPages) {

        if ($page != $text) {
            $res = <<<html
            <span class="pagination__link pagination__link--inactive">
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
        pageLink(-1, "<<", $nbPages, false),
        pageLink(-1, "<", $nbPages, false),
        pageLink(1, "1", $nbPages, true, true),
        pageLink(-1, ">", $nbPages, false),
        pageLink(-1, ">>", $nbPages, false)
    ];
} else if ($page == 1) {
    $paginationLinks = [
        pageLink(-1, "<<", $nbPages, false),
        pageLink(-1, "<", $nbPages, false),
        pageLink(1, "1", $nbPages, true, true),
        pageLink(2, "2", $nbPages),
        pageLink(3, "3", $nbPages),
        pageLink(2, ">", $nbPages),
        pageLink($nbPages, ">>", $nbPages)
    ];
} else if ($page >= $nbPages) {
    $paginationLinks = [
        pageLink(1, "<<", $nbPages),
        pageLink($page - 1, "<", $nbPages),
        pageLink($page - 2, $page - 2, $nbPages),
        pageLink($page - 1, $page - 1, $nbPages),
        pageLink($page, $page, $nbPages, true, true),
        pageLink(-1, ">", $nbPages, false),
        pageLink(-1, ">>", $nbPages, false)
    ];
} else {
    $paginationLinks = [
        pageLink(1, "<<", $nbPages),
        pageLink($page - 1, "<", $nbPages),
        pageLink($page - 1, $page - 1, $nbPages),
        pageLink($page, $page, $nbPages, true, true),
        pageLink($page + 1, $page + 1, $nbPages),
        pageLink($page + 1, ">", $nbPages),
        pageLink($nbPages, ">>", $nbPages)
    ];
}
?>

<div class="panel" id="listeQuestions">
    <h1>Questions : </h1>

    <div class="searchBar">
        <form action="frontController.php?controller=question&action=listerQuestions" method="post">
            <input type="text" name="search" placeholder="Rechercher une question">
            <input type="submit" value="Rechercher">
        </form>
    </div>

    //TODO: supprimer le style inline
    <div id="questions">
        <?php echo implode("", $questionHTMLs); ?>
    </div>


    <div class="pagination">
        <?php echo implode("", $paginationLinks); ?>
    </div>

</div>