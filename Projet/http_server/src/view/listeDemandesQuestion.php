<main>



    <?php
    $i = 0;
    foreach ($demandes as $q) {
        $i++;
        echo "<div class='demande' style='--order: " . $i . "'>";
        echo ("<h3>" . $q->getTitre() . "</h3>");
        echo ("<p><em>" . $q->getIntitule() . "</em></p>");
        echo ("- " . $q->getOrganisateur()->getPrenom() . " " . $q->getOrganisateur()->getNom());

        echo ("<p> Demande à valider par l'admin </p>");
        echo "<div class='boutons'>";
        echo ("<a href='frontController.php?action=refuserDemandeQuestion&idQuestion=" . $q->getIdQuestion() . "'>Refuser</a>");
        echo ("<a href='frontController.php?action=accepterDemandeQuestion&idQuestion=" . $q->getIdQuestion() . "'>Valider</a>");
        echo "</div>";

        echo "</div>";
    }

    ?>

</main>