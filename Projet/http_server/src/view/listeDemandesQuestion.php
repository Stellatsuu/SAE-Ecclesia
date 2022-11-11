<div class="panel">
    <h1>Questions à valider</h1>

    <?php
        $i = 0;
        foreach ($demandes as $q) {
            $i++;
            echo "<div class='demandeQuestion'><div class='boite' style='--order: " . $i . "'>";
            echo ("<h2>" . htmlspecialchars($q->getTitre()) . "</h2>");
            echo ("<p>" . htmlspecialchars($q->getDescription()) . "</p>");
            echo ("<p>- " . htmlspecialchars($q->getOrganisateur()->getPrenom()) . " " . htmlspecialchars(strtoupper($q->getOrganisateur()->getNom()))) . "</p>";

            echo "</div>";
            echo "<div class='boite'>";
            echo ("<a class='button refuserBtn' href='frontController.php?action=refuserDemandeQuestion&idQuestion=" . rawurlencode($q->getIdQuestion()) . "'>Refuser</a>");
            echo ("<a class='button validerBtn' href='frontController.php?action=accepterDemandeQuestion&idQuestion=" . rawurlencode($q->getIdQuestion()) . "'>Valider</a>");
            echo "</div></div>";
        }
    ?>
</div>