<?php
    $existeProposition = !empty($proposition);

    if($existeProposition){
        $paragraphes = $proposition->getParagraphes();
    }
?>

<form class="panel" method="post" action="frontController.php?controller=proposition&action=ecrireProposition" id="ecrirePropositionForm">
    <h1>Écrire proposition</h1>
    <fieldset>
        <label for="titreProposition">Nom de la proposition : </label>
        <div class="text_input_div">
            <input type="text" id="titreProposition" name="titreProposition" maxlength="100" <?= $existeProposition ? "value=\"{$proposition->getTitreProposition()}\"" : "" ?> required/>
            <span class="indicateur_max_chars  unselectable">100 max</span>
        </div>

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

                if($existeProposition){
                    foreach($paragraphes as $paragraphe){
                        if($paragraphe->getSection()->getIdSection() == $section->getIdSection()){
                            echo htmlspecialchars($paragraphe->getContenuParagraphe());
                            break;
                        }
                    }
                }

                echo "</textarea>";
                if($existeProposition){
                    echo "<input type='hidden' name='section_" . $i . "_idParagraphe' value='" . htmlspecialchars($paragraphe->getIdParagraphe()) . "'/>";
                }
            }
        ?>
    </fieldset>

    <input type="number" name="idResponsable" <?= $existeProposition ? "value=\"{$proposition->getRedacteur()->getIdUtilisateur()}\"" : "" ?> required/>
    <?= $existeProposition ? "<input type=\"hidden\" name=\"idProposition\" value=\"{$proposition->getidProposition()}\"/>" : "" ?>
    <input type="hidden" name="idQuestion" value="<?= $question->getIdQuestion() ?>" />
    <input type="submit" value="Enregistrer"/>
</form>