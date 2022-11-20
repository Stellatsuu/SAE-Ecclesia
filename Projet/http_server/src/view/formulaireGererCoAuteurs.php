<form method="post" action="frontController.php?action=gererCoAuteurs&controller=proposition" class="panel">
    <h1>Co-auteurs :</h1>
    <fieldset>

        <div id="co_auteurs_input">
            <label for="co_auteurs_input">Co-auteurs :</label>

            <button type="button" id="add_co_auteur">+</button>
        </div>

        <input type="hidden" name="idProposition" value="<?= $proposition->getIdProposition() ?>">
        <input type="submit" value="Valider">
    </fieldset>
</form>


<script>
    const utilisateurs = <?= json_encode($utilisateursAutorises) ?>;
    console.log(utilisateurs);
    const options = '<option value="" selected disabled>---</option>' + utilisateurs.map(utilisateur => `<option value="${utilisateur.idUtilisateur}">${(utilisateur.nom).toUpperCase()} ${utilisateur.prenom}</option>`).join("\n");

</script>
<script type="module" src="js/co_auteurs.js"></script>