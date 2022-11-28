<form method="post" action="frontController.php?action=gererCoAuteurs&controller=coAuteur" class="panel" id="gererCoAuteursForm">
    <h1>Co-auteurs de la proposition "<?= htmlspecialchars($proposition->getTitreProposition()) ?>" :</h1>
    <fieldset>

        <label for="co_auteurs_input">Co-auteurs :</label>
        <div id="co_auteurs_input">


            <button type="button" id="add_co_auteur">+</button>
        </div>

        <input type="hidden" name="idProposition" value="<?= $proposition->getIdProposition() ?>">
    </fieldset>

    <input type="submit" value="Valider">

    <h2>Demandes de participation:</h2>

    <?php

    use App\SAE\Model\DataObject\DemandeCoAuteur;

    foreach ($demandesCoAuteur as $demande) {

        $demande = DemandeCoAuteur::toDemandeCoAuteur($demande);
        $idDemandeur = $demande->getIdDemandeur();
        $demandeur = $demande->getDemandeur();
        $nomComplet = $demandeur->getPrenom() . " " . strtoupper($demandeur->getNom());
        $message = $demande->getMessage();
        $idProposition = $demande->getIdProposition();

        $html = <<<HTML
        <div class="boite demandeCoAuteur">
            <p>$message</p>
            <p>$nomComplet</p>
            <div>
                <a href="frontController.php?action=accepterDemandeCoAuteur&controller=coAuteur&idDemandeur=$idDemandeur&idProposition=$idProposition" class="button validerBtn">Accepter</a>
                <a href="frontController.php?action=refuserDemandeCoAuteur&controller=coAuteur&idDemandeur=$idDemandeur&idProposition=$idProposition" class="button refuserBtn">Refuser</a>
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
    console.log(utilisateurs);
    const options = '<option value="" selected disabled>---</option>' + utilisateurs.map(utilisateur => `<option value="${utilisateur.idUtilisateur}">${(utilisateur.nom).toUpperCase()} ${utilisateur.prenom}</option>`).join("\n");
</script>
<script type="module" src="js/co_auteurs.js"></script>