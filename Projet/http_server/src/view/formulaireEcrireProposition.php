<?php
    $existeProposition = !empty($proposition);

    if($existeProposition){
        $paragraphes = $proposition->getParagraphes();
    }
?>

<form class="panel" method="post" action="frontController.php?controller=proposition&action=ecrireProposition">
    <h1><?= htmlspecialchars($question->getTitre()) ?></h1>
    <p><?= htmlspecialchars($question->getDescription()) ?></p>
    <label for="titreProposition">Nom de la proposition : </label>
    <input type="text" id="titreProposition" name="titreProposition" maxlength="100" <?= $existeProposition ? "value=\"{$proposition->getTitreProposition()}\"" : "" ?> required/>

    <?php


        foreach($question->getSections() as $section){
            echo "
                <h2>" . htmlspecialchars($section->getNomSection()) . "</h2>
                <p>" . htmlspecialchars($section->getDescriptionSection()) . "</p>
                <textarea name='section_" . htmlspecialchars($section->getIdSection()) . "'>";

            if($existeProposition){
                foreach($paragraphes as $paragraphe){
                    if($paragraphe->getSection()->getIdSection() == $section->getIdSection()){
                        echo $paragraphe->getContenuParagraphe();
                        break;
                    }
                }
            }

            echo "</textarea>
            ";
        }
    ?>

    <input type="number" name="idResponsable" <?= $existeProposition ? "value=\"{$proposition->getRedacteur()->getIdUtilisateur()}\"" : "" ?> required/>
    <input type="hidden" name="idQuestion" value="<?= $question->getIdQuestion() ?>" />

    <input type="submit" value="Enregistrer"/>
</form>