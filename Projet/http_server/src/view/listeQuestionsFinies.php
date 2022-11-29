<div class="panel" id="listeQuestionsFinies">
    <?php

    use App\SAE\Model\DataObject\Question;

    foreach ($questions as $q) {
        $q = Question::castIfNotNull($q);

        $titre = htmlspecialchars($q->getTitre());
        $description = htmlspecialchars($q->getDescription());
        $idQuestion = rawurlencode($q->getIdQuestion());
        $dateFinVotes = $q->getDateFermetureVotes()->format("d/m/Y");
        $organisateur = "- " . $q->getOrganisateur()->getNom() . " " . $q->getOrganisateur()->getPrenom();

        $html = <<<HTML
        <div class="question">
            <div class="boite" id="questionFinie">
                <div>
                    <a href="frontController.php?controller=question&action=afficherResultats&idQuestion=$idQuestion"><h2>$titre</h2></a>    
                    <p>$description</p>
                    <p id="organisateur">$organisateur</p>
                    <p id="date">$dateFinVotes</p>
                </div>
            </div>
        </div>
        HTML;
        echo $html;
    }
    ?>
</div>