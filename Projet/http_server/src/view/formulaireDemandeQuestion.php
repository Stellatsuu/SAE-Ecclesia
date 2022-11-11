<form method="post" action="frontController.php?action=demanderCreationQuestion" class="panel">
    <h1>Proposer une question</h1>
    <fieldset>
        <label for="titre_id">Question :</label>
         <input type="text" name="titre" maxlength="100" placeholder="Écrivez le titre de votre question ici" id="titre_id" required>

         <label for="description_id">Description :</label>
         <textarea rows=6 cols=50 id="description_id" placeholder="Écrivez les détails de votre question ici, la raison de cette demande, etc" name="description" required></textarea>
    </fieldset>

        <label for="idUtilisateur_id"></label>
        <input type="number" name="idUtilisateur" id="idUtilisateur_id" required>
        <input type="submit" value="Valider">
</form>