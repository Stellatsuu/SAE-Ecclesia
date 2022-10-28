<main>

    <h1>Mes Questions</h1>
    <?php
        if($message){
            require('message.php');
        }
    ?>
    

    <?php

    $i = 0;
    foreach ($questions as $q) {
        $i++;
        echo "<div class='question'><div class='boite' style='--order: " . $i . "'>";
        echo ("<h2>" . $q->getTitre() . "</h2>");
        echo ("<p>" . $q->getIntitule() . "</p>");

        echo "<a href='frontController.php?action=afficherFormulairePoserQuestion&idQuestion=" . $q->getIdQuestion() . "'>Éditer</a>";

        echo "</div></div>";
    }

    ?>

    

</main>