<main>

    <h1>Mes Questions</h1>


    <?php

    use App\SAE\Model\DataObject\Question;
    use App\SAE\Lib\PhaseQuestion as Phase;

    $i = 0;
    foreach ($questions as $q) {
        $q = Question::toQuestion($q);
        $phase = $q->getPhase();
        $i++;
        echo "<div class='question'><div class='boite' style='--order: " . $i . "'>";
        echo ("<h2>" . htmlspecialchars($q->getTitre()) . "</h2>");
        echo ("<p>" . htmlspecialchars($q->getDescription()) . "</p>");
        echo ("<p>Phase : " . $phase->toString() . "</p>");

        echo "<a href='frontController.php?controller=question&action=afficherFormulairePoserQuestion&idQuestion=" . rawurlencode($q->getIdQuestion()) . "'>Éditer</a>";

        echo "</div></div>";
    }

    ?>



</main>