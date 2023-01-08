<?php

use App\SAE\Lib\Markdown;

$paragraphes = $proposition->getParagraphes();
$question = $proposition->getQuestion();
?>

<form class="panel" method="post" action="frontController.php?controller=proposition&action=contribuerProposition" id="ecrirePropositionForm">
    <h1>Contribuer proposition</h1>
    <fieldset>
        <label>Nom de la proposition : </label>
        <div class="text_input_div">
            <input type="text" id="titreProposition" name="titreProposition" maxlength="100" value="<?= htmlspecialchars($proposition->getTitreProposition()) ?>" required />
            <span class="indicateur_max_chars  unselectable">100 max</span>
        </div>


        <?php
        $sections = $question->getSections();
        for ($i = 0; $i < count($sections); $i++) {
            $section = $sections[$i];
            $nomSection = htmlspecialchars($section->getNomSection());
            $descriptionSection = Markdown::toHtml($section->getDescriptionSection());

            $html = <<<HTML
            <details>
                <summary class="titre-section">$nomSection</summary>
                <div class='description-section markdown'>$descriptionSection</div>
            </details>
            <textarea name="section_$i">
            HTML;

            echo $html;

            $paragraphe = null;
            foreach ($paragraphes as $p) {
                if ($p->getSection()->getIdSection() == $section->getIdSection()) {
                    echo htmlspecialchars($p->getContenuParagraphe());
                    $paragraphe = $p;
                    break;
                }
            }

            echo '</textarea>
                <input type="hidden" name="section_' . $i . '_idParagraphe" value="' . (isset($paragraphe) ? htmlspecialchars($paragraphe->getIdParagraphe()) : "-1") . '"/>
            ';
        }
        ?>
    </fieldset>

    <input type="hidden" name="idProposition" value="<?= htmlspecialchars($proposition->getidProposition()) ?>" />
    <input type="submit" value="Enregistrer" />
</form>
