<div class="panel" id="listeDemandesQuestions">
    <h1>Questions à valider</h1>

    <?php

    use App\SAE\Lib\Markdown;

    //$dataDemandes

    foreach ($dataDemandes as $demande) {
        $titre = $demande['titre'];
        $description = Markdown::toHtml($demande['description']);
        $nomUsuelOrganisateur = $demande['nomUsuelOrganisateur'];
        $idQuestion = $demande['idQuestion'];

        echo <<<HTML
            <div class="demandeQuestion acceptOrDeny">
                <div class="boite">
                    <h2>$titre</h2>
                    <div class="markdown">$description</div>
                    <p>- $nomUsuelOrganisateur</p>
                </div>
                <div class="boite">
                    <a class="button refuserBtn" href="frontController.php?controller=demandeQuestion&action=refuserDemandeQuestion&idQuestion=$idQuestion">Refuser</a>
                    <a class="button validerBtn" href="frontController.php?controller=demandeQuestion&action=accepterDemandeQuestion&idQuestion=$idQuestion">Valider</a>
                </div>
            </div>
        HTML;
    }
    ?>
</div>