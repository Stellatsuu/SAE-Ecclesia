<div class="panel" id="listeDemandesQuestions">
    <h1>Questions à valider</h1>

    <?php

    use App\SAE\Lib\Markdown;

    $i = 0;
        foreach ($demandes as $q) {
            $i++;
            echo "<div class='demandeQuestion acceptOrDeny'><div class='boite' style='--order: " . $i . "'>";
            echo ("<h2>" . htmlspecialchars($q->getTitre()) . "</h2>");
            echo ("<span class=\"markdown\">" . Markdown::toHtml($q->getDescription()) . "</span>");
            echo ("<p>- " . htmlspecialchars($q->getOrganisateur()->getPrenom()) . " " . htmlspecialchars(strtoupper($q->getOrganisateur()->getNom()))) . "</p>";

            echo "</div>";
            echo "<div class='boite'>";
            echo ("<a class='button refuserBtn' href='frontController.php?controller=demandeQuestion&action=refuserDemandeQuestion&idQuestion=" . rawurlencode($q->getIdQuestion()) . "'>Refuser</a>");
            echo ("<a class='button validerBtn' href='frontController.php?controller=demandeQuestion&action=accepterDemandeQuestion&idQuestion=" . rawurlencode($q->getIdQuestion()) . "'>Valider</a>");
            echo "</div></div>";
        }
    ?>
</div>