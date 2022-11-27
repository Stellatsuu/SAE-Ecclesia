<?php
    $paragraphes = $proposition->getParagraphes();
    $question = $proposition->getQuestion();
?>

<form class="panel" method="post" action="frontController.php?controller=proposition&action=contribuerProposition" id="ecrirePropositionForm">
    <h1>Contribuer proposition</h1>
    <fieldset>
        <label for="titreProposition">Nom de la proposition : </label>
        <div class="text_input_div">
            <input type="text" id="titreProposition" name="titreProposition" maxlength="100" value="<?= $proposition->getTitreProposition() ?>" required/>
            <span class="indicateur_max_chars  unselectable">100 max</span>
        </div>


        <?php
        $sections = $question->getSections();
        for ($i=0; $i < count($sections); $i++) {
            $section = $sections[$i];
            echo "
                <input type='checkbox' id='deploy_" . $i . "' class='texteDepliantTrigger'/>
                <div class='sectionTitle'>
                    <h2>" . htmlspecialchars($section->getNomSection()) . "</h2>
                    <label for='deploy_" . $i . "'>
                        <img src='./assets/images/arrow.svg' class='arrow' alt='open and close arrow'/>
                    </label>
                </div>
                <p class='descriptionProposition'>" . htmlspecialchars($section->getDescriptionSection()) . "</p>
                <textarea name='section_" . $i . "'>";

                $paragraphe = null;
                foreach($paragraphes as $p){
                    if($p->getSection()->getIdSection() == $section->getIdSection()){
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

    <input type="number" name="idCoAuteur" required/>
    <input type="hidden" name="idProposition" value="<?= htmlspecialchars($proposition->getidProposition()) ?>"/>
    <input type="submit" value="Enregistrer"/>
</form>