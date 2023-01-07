<form method="post" action="frontController.php?action=gererCoAuteurs&controller=coAuteur" class="panel" id="gererCoAuteursForm">
    <h1>Co-auteurs de la proposition "<?= htmlspecialchars($proposition->getTitreProposition()) ?>" :</h1>
    <fieldset>

        <label for="co_auteurs_input">Co-auteurs :</label>
        <div id="co_auteurs_input">


            <button type="button" id="add_co_auteur">+</button>
        </div>

        <input type="hidden" name="idProposition" value="<?= htmlspecialchars($proposition->getIdProposition()) ?>">
    </fieldset>

    <input type="submit" value="Valider">

    <h2>Demandes de participation:</h2>

    <?php

    use App\SAE\Model\DataObject\DemandeCoAuteur;

    foreach ($demandesCoAuteur as $demande) {

        $demande = DemandeCoAuteur::castIfNotNull($demande);
        $usernameDemandeur = htmlspecialchars($demande->getUsernameDemandeur());
        $demandeur = $demande->getDemandeur();
        $nomComplet = htmlspecialchars($demandeur->getPrenom() . " " . strtoupper($demandeur->getNom()));
        $message = htmlspecialchars($demande->getMessage());
        $idProposition = htmlspecialchars($demande->getIdProposition());

        $html = <<<HTML
        <div class="demandeCoAuteur acceptOrDeny">
            <div class="boite">
                <p>$nomComplet</p>
                <p>$message</p>
            </div>
            <div class="boite">
                <a href="frontController.php?action=accepterDemandeCoAuteur&controller=coAuteur&usernameDemandeur=$usernameDemandeur&idProposition=$idProposition" class="button validerBtn">Accepter</a>
                <a href="frontController.php?action=refuserDemandeCoAuteur&controller=coAuteur&usernameDemandeur=$usernameDemandeur&idProposition=$idProposition" class="button refuserBtn">Refuser</a>
            </div>
        </div>
        HTML;

        echo $html;
    }

    ?>


</form>


<script>
    const utilisateurs = <?= json_encode($utilisateursAutorises) ?>;
    const coAuteurs = <?= json_encode($coAuteurs) ?>;
    const options = '<option value="" selected disabled>---</option>' + utilisateurs.map(utilisateur => `<option value="${utilisateur.username}">${(utilisateur.nom).toUpperCase()} ${utilisateur.prenom}</option>`).join("\n");
</script>
<script type="module" src="js/co_auteurs.js"></script>
