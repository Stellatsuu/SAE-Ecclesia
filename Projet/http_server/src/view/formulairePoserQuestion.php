<main>
    <form method="post" action="../../../web/frontController.php?action=poserQuestion">
        <fieldset>
            <legend>Posez votre question :</legend>
            <p>
                <label for="titre_id">
                    <h3>Question :</h3>
                </label>
                <textarea readonly rows=6 cols=50 id="titre_id" value=<?= $titre ?> name="titre" required></textarea>
            </p>
            <p>
                <label for="intitule_id">
                    <h3>Intitulé :</h3>
                </label>
                <textarea rows=6 cols=50 id="intitule_id" placeholder="Écrivez les détails de votre question ici, la raison de cette demande, etc" name="intitule" required></textarea>
            </p>
            <div id="sections_input">
                



            </div>





















            <p>
                <input type="submit" value="Envoyer" />
            </p>
        </fieldset>
    </form>
</main>