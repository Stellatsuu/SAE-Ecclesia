<form method="post" action="frontController.php?controller=coAuteur&action=demanderCoAuteur" class="panel">
    <h1>Demander à être co-auteur</h1>
    <h2><?= htmlspecialchars($proposition->getTitreProposition()) ?></h2>
    <fieldset>

        <label for="message_id">Message :</label>
        <div class="text_input_div">
            <textarea rows=6 cols=50 id="message_id" maxlength="1000" placeholder="Écrivez un message à l'organisateur de la question" name="message"></textarea>
            <span class="indicateur_max_chars unselectable">1000 max</span>
        </div>

    </fieldset>

    <input type="hidden" name="idProposition" value="<?= htmlspecialchars($proposition->getIdProposition()) ?>">

    <input type="submit" value="Valider">
</form>
