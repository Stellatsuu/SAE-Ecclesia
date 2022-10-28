<div class="panel">
    <?php
        if($message){
            require('message.php');
        }
    ?>
    <h1>Questions à valider</h1>

    <?php
        $i = 0;
        foreach ($demandes as $q) {
            $i++;
            echo "<div class='demandeQuestion'><div class='boite' style='--order: " . $i . "'>";
            echo ("<h2>" . $q->getTitre() . "</h2>");
            echo ("<p>" . $q->getIntitule() . "</p>");
            echo ("<p>- " . $q->getOrganisateur()->getPrenom() . " " . $q->getOrganisateur()->getNom()) . "</p>";

            echo "</div>";
            echo "<div class='boite'>";
            echo ("<a class='button refuserBtn' href='frontController.php?action=refuserDemandeQuestion&idQuestion=" . $q->getIdQuestion() . "'>Refuser</a>");
            echo ("<a class='button validerBtn' href='frontController.php?action=accepterDemandeQuestion&idQuestion=" . $q->getIdQuestion() . "'>Valider</a>");
            echo "</div></div>";
        }
    ?>
</div>