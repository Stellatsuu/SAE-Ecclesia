<form method="post" action="frontController.php?controller=demandeQuestion&action=demanderCreationQuestion" class="panel">
    <h1>Proposer une question</h1>
    <fieldset>
        <label for="titre_id">Question :</label>
        <div class="text_input_div">
            <input type="text" name="titre" maxlength="100" placeholder="Écrivez le titre de votre question ici" id="titre_id" required>
            <span class="indicateur_max_chars  unselectable">100 max</span>
        </div>

        <label for="description_id">Description :</label>
        <div class="text_input_div">
            <textarea rows=6 cols=50 id="description_id" maxlength="4000" placeholder="Écrivez les détails de votre question ici, la raison de cette demande, etc" name="description" required></textarea>
            <span class="indicateur_max_chars unselectable">4000 max</span>
        </div>


    </fieldset>

    <input type="submit" value="Valider">
</form>