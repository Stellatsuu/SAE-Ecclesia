<form method="post" action="frontController.php?action=gererCoAuteurs&controller=proposition" class="panel" id="gererCoAuteursForm">
    <h1>Co-auteurs de la proposition "<?= htmlspecialchars($proposition->getTitreProposition()) ?>" :</h1>
    <fieldset>

        <label for="co_auteurs_input">Co-auteurs :</label>
        <div id="co_auteurs_input">


            <button type="button" id="add_co_auteur">+</button>
        </div>

        <input type="hidden" name="idProposition" value="<?= $proposition->getIdProposition() ?>">
    </fieldset>

    <input type="submit" value="Valider">
</form>


<script>
    const utilisateurs = <?= json_encode($utilisateursAutorises) ?>;
    const coAuteurs = <?= json_encode($coAuteurs) ?>;
    console.log(utilisateurs);
    const options = '<option value="" selected disabled>---</option>' + utilisateurs.map(utilisateur => `<option value="${utilisateur.idUtilisateur}">${(utilisateur.nom).toUpperCase()} ${utilisateur.prenom}</option>`).join("\n");

</script>
<script type="module" src="js/co_auteurs.js"></script>