<div class="panel" id="listeQuestionsFinies">
    <h1>Questions terminées</h1>
    <?php

    use App\SAE\Model\DataObject\Question;

    foreach ($questions as $q) {
        $q = Question::toQuestion($q);

        $titre = htmlspecialchars($q->getTitre());
        $description = htmlspecialchars($q->getDescription());
        $idQuestion = rawurlencode($q->getIdQuestion());
        $dateFinVotes = $q->getDateFermetureVotes()->format("d/m/Y");
        $organisateur = "- " . $q->getOrganisateur()->getNom() . " " . $q->getOrganisateur()->getPrenom();

        $html = <<<HTML
        <div class="question">
            <div class="boite">
                <div>
                    <div>
                        <a href="frontController.php?controller=question&action=afficherResultats&idQuestion=$idQuestion"><h2>$titre</h2></a> 
                        <p class="date">$dateFinVotes</p>
                    </div>   
                    <p>$description</p>
                    <p class="organisateur">$organisateur</p>
                </div>
            </div>
        </div>
        HTML;
        echo $html;
    }
    ?>
</div>