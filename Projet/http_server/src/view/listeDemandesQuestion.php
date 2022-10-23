<main>



    <?php
    $i = 0;
    foreach ($demandes as $q) {
        $i++;
        echo "<div class='demande' style='--order: " . $i ."'>";
        echo ("<h3>" . $q->getTitre() . "</h3>");
        echo ("<p><em>" . $q->getIntitule() . "</em></p>");
        echo ("- " . $q->getOrganisateur()->getPrenom() . " " . $q->getOrganisateur()->getNom());

        if ($q->getEstValide()) {
            echo ("<p> Demande validée par l'admin </p>");
        } else {
            echo ("<p> Demande à valider par l'admin </p>");
            echo "<div class='boutons'>";
            echo ("<a href='frontController.php?action=refuserQuestion&idQuestion=" . $q->getIdQuestion() . "'>Refuser</a>");
            echo ("<a href='frontController.php?action=accepterQuestion&idQuestion=" . $q->getIdQuestion() . "'>Valider</a>");
            echo "</div>";
        }

        echo "</div>";
    }

    ?>

</main>