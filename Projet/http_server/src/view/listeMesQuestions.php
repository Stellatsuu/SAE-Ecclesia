<main>

    <h1>Mes Questions</h1>
    

    <?php

    $i = 0;
    foreach ($questions as $q) {
        $i++;
        echo "<div class='question'><div class='boite' style='--order: " . $i . "'>";
        echo ("<h2>" . htmlspecialchars($q->getTitre()) . "</h2>");
        echo ("<p>" . htmlspecialchars($q->getDescription()) . "</p>");

        echo "<a href='frontController.php?controller=question&action=afficherFormulairePoserQuestion&idQuestion=" . rawurlencode($q->getIdQuestion()) . "'>Éditer</a>";

        echo "</div></div>";
    }

    ?>

    

</main>