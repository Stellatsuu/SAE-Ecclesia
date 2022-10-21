<main>



    <?php

    foreach ($questions as $q) {
        echo "<div class='question'>";
        echo ("<h3>" . $q->getQuestion() . "</h3>");
        echo ("<p><em>" . $q->getIntitule() . "</em></p>");
        echo ("- " . $q->getOrganisateur()->getPrenom() . " " . $q->getOrganisateur()->getNom());

        if ($q->getEstValide()) {
            echo ("<p> Question validée par l'admin </p>");
        } else {
            echo ("<p> Question à valider par l'admin </p>");
            echo "<div class='boutons'>";
            echo ("<a href='frontController.php?action=refuserQuestion&idQuestion=" . $q->getIdQuestion() . "'>Refuser</a>");
            echo ("<a href='frontController.php?action=accepterQuestion&idQuestion=" . $q->getIdQuestion() . "'>Valider</a>");
            echo "</div>";
        }

        echo "</div>";
    }

    ?>

</main>