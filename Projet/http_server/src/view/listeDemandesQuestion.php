<div class="panel" id="listeDemandesQuestions">
    <h1>Questions à valider</h1>

    <?php

    use App\SAE\Lib\Markdown;

    $i = 0;
        foreach ($demandes as $q) {
            $i++;
            $titre = htmlspecialchars($q->getTitre());
            $description = Markdown::toHtml($q->getDescription());
            $nomOrganisateur = htmlspecialchars($q->getOrganisateur()->getPrenom()) . " " . htmlspecialchars(strtoupper($q->getOrganisateur()->getNom()));
            $idQuestionUrl = rawurlencode($q->getIdQuestion());

            echo <<<HTML
                <div class="demandeQuestion acceptOrDeny">
                    <div class="boite" style="--order: $i;">
                        <h2>$titre</h2>
                        <div class="markdown">$description</div>
                        <p>- $nomOrganisateur</p>
                    </div>
                    <div class="boite">
                        <a class="button refuserBtn" href="frontController.php?controller=demandeQuestion&action=refuserDemandeQuestion&idQuestion=$idQuestionUrl">Refuser</a>
                        <a class="button validerBtn" href="frontController.php?controller=demandeQuestion&action=accepterDemandeQuestion&idQuestion=$idQuestionUrl">Valider</a>
                    </div>
                </div>
            HTML;
        }
    ?>
</div>
