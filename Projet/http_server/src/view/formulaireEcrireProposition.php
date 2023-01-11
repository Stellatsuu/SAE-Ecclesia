<?php

use App\SAE\Lib\Markdown;

$existeProposition = !empty($proposition);

if ($existeProposition) {
    $paragraphes = $proposition->getParagraphes();
}
?>

<form class="panel" method="post" action="frontController.php?controller=proposition&action=ecrireProposition" id="ecrirePropositionForm">
    <h1>Écrire proposition</h1>
    <fieldset>
        <label for="titreProposition">Nom de la proposition : </label>
        <div class="text_input_div">
            <input type="text" id="titreProposition" name="titreProposition" maxlength="100" <?= $existeProposition ? "value=\"{$proposition->getTitreProposition()}\"" : "" ?> required />
            <span class="indicateur_max_chars unselectable">100 max</span>
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

            if ($existeProposition) {
                foreach ($paragraphes as $paragraphe) {
                    if ($paragraphe->getSection()->getIdSection() == $section->getIdSection()) {
                        echo htmlspecialchars($paragraphe->getContenuParagraphe());
                        break;
                    }
                }
            }

            echo "</textarea>";
            if ($existeProposition) {
                echo "<input type='hidden' name='section_" . $i . "_idParagraphe' value='" . htmlspecialchars($paragraphe->getIdParagraphe()) . "'/>";
            }
        }
        ?>
    </fieldset>

    <?= $existeProposition ? "<input type=\"hidden\" name=\"idProposition\" value=\"" . htmlspecialchars($proposition->getidProposition()) . "\"/>" : "" ?>
    <input type="hidden" name="idQuestion" value="<?= htmlspecialchars($question->getIdQuestion()) ?>" />
    <input type="submit" value="Enregistrer" />
</form>
