<main>

    <?php
    if ($question) {
        $idQuestion = $question->getIdQuestion();
        echo ("idQuestion : " . $idQuestion . "<br>");
        echo ("Question : " . $question->getQuestion() . "<br>");
        echo ("Intitulé : " . $question->getIntitule() . "<br>");
        echo ("Organisateur : " . $question->getOrganisateur()->getNom() . " " . $question->getOrganisateur()->getPrenom() . "<br><br>");
    } else {
        $idQuestion = -1;
        echo ("Aucune question à valider");
    }
    ?>

    <form <?php if($question === null) {echo "hidden";} ?> method="post" action="../../web/frontController.php?action=accepterQuestion">
        <fieldset>
            <input type="hidden" name="idQuestion" value="<?php echo $idQuestion; ?>">
            <input type="submit" value="Accepter">
        </fieldset>
    </form>
    <form <?php if($question === null) {echo "hidden";} ?> method="post" action="../../web/frontController.php?action=refuserQuestion">
        <fieldset>
            <input type="hidden" name="idQuestion" value="<?php echo $idQuestion; ?>">
            <input type="submit" value="Refuser">
        </fieldset>
    </form>


</main>