<div class="panel">
    <?php

    use App\SAE\Model\DataObject\Question;

    foreach ($questions as $q) {
        $q = Question::castIfNotNull($q);

        $titre = htmlspecialchars($q->getTitre());
        $description = htmlspecialchars($q->getDescription());
        $idQuestion = rawurlencode($q->getIdQuestion());
        $dateFinVotes = $q->getDateFermetureVotes()->format("d/m/Y");

        $html = <<<HTML
        <div class="question">
            <div class="boite">
                <div>
                    <a href="frontController.php?controller=question&action=afficherResultats&idQuestion=$idQuestion"><h2>$titre</h2></a>    
                    <p>$description</p>
                    <p>$dateFinVotes</p>
                </div>
            </div>
        </div>
        HTML;
        echo $html;
    }
    ?>
</div>