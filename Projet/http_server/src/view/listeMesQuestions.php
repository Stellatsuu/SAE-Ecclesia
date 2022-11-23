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

        echo "<a class='button' href='frontController.php?controller=question&action=afficherFormulairePoserQuestion&idQuestion=" . rawurlencode($q->getIdQuestion()) . "'>Éditer</a>";

        switch ($phase) {
            case Phase::Attente:
                echo "<a class='button' href='frontController.php?controller=question&action=passagePhaseRedaction&idQuestion=" . rawurlencode($q->getIdQuestion()) . "'>Passer à la phase de rédaction</a>";
                break;
            
            case Phase::Redaction:
                echo "<a class='button' href='frontController.php?controller=question&action=passagePhaseVote&idQuestion=" . rawurlencode($q->getIdQuestion()) . "'>Passer à la phase de vote</a>";
                break;

            case Phase::Lecture:
                echo "<a class='button' href='frontController.php?controller=question&action=passagePhaseVote&idQuestion=" . rawurlencode($q->getIdQuestion()) . "'>Passer à la phase de vote</a>";
                break;
        }
        
        echo "</div></div>";
    }

    ?>



</main>