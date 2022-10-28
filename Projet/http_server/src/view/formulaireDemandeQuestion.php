<form method="post" action="frontController.php?action=demanderCreationQuestion" class="panel">
    <h1>Proposer une question</h1>
    <fieldset>
        <label for="titre_id">Question :</label>
         <textarea rows=6 cols=50 id="titre_id" placeholder="Écrivez votre question ici" name="titre" required></textarea>

         <label for="intitule_id">Intitulé :</label>
         <textarea rows=6 cols=50 id="intitule_id" placeholder="Écrivez les détails de votre question ici, la raison de cette demande, etc" name="intitule" required></textarea>
    </fieldset>

        <label for="idUtilisateur_id"></label>
        <input type="number" name="idUtilisateur" id="idUtilisateur_id" required>
        <input type="submit" value="Valider">
</form>