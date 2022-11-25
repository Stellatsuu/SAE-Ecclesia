<?php
    $paragraphes = $proposition->getParagraphes();
    $question = $proposition->getQuestion();
?>

<form class="panel" method="post" action="frontController.php?controller=proposition&action=contribuerProposition" id="ecrirePropositionForm">
    <h1>Écrire proposition</h1>
    <fieldset>
        <h2><?= htmlspecialchars($proposition->getTitreProposition()) ?></h2>

        <?php
        $sections = $question->getSections();
        for ($i=0; $i < count($sections); $i++) {
            $section = $sections[$i];
            echo "
                <input type='checkbox' id='deploy_" . $i . "' class='sectionTitleCheckbox'/>
                <div class='sectionTitle'>
                    <h2>" . htmlspecialchars($section->getNomSection()) . "</h2>
                    <label for='deploy_" . $i . "'>
                        <img src='./assets/images/arrow.svg' class='arrow' alt='open and close arrow'/>
                    </label>
                </div>
                <p class='descriptionProposition'>" . htmlspecialchars($section->getDescriptionSection()) . "</p>
                <textarea name='section_" . $i . "'>";

                foreach($paragraphes as $paragraphe){
                    if($paragraphe->getSection()->getIdSection() == $section->getIdSection()){
                        echo htmlspecialchars($paragraphe->getContenuParagraphe());
                        break;
                    }
                }

            echo "</textarea>
                <input type='hidden' name='section_" . $i . "_idParagraphe' value='" . htmlspecialchars($paragraphe->getIdParagraphe()) . "'/>
            ";
        }
        ?>
    </fieldset>

    <input type="number" name="idCoAuteur" required/>
    <input type="hidden" name="idProposition" value="<?= htmlspecialchars($proposition->getidProposition()) ?>"/>
    <input type="submit" value="Enregistrer"/>
</form>