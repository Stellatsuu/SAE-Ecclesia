<main>
    <form method="post" action="../../web/frontController.php?action=attribuerQuestion">
        <fieldset>
            <legend>Attribuer une question</legend>

            <p>Ici, l'administrateur remplit le formulaire pour le client.
                Dans la vraie version, l'intitulé, la question et l'organisateur
                seraient déjà enregistrés en tant que <b>demande de question</b> et
                l'admin aurait simplement à <b>valider ou refuser</b> la question</p>

            <label for="question">Question</label>
            <p>
                <input type="text" name="question" id="question" required>
            </p>

            <label for="intitule">Intitulé</label>
            <p>
                <input type="text" name="intitule" id="intitule" required>
            </p>

            <label for="organisateur">ID Organisateur</label>
            <p>
                <input type="number" name="organisateur" id="organisateur" required>
            </p>

            <label for="estValide">Est valide</label>
            <p>
                <input type="checkbox" name="estValide" id="estValide">
            </p>

            <input type="submit" value="Envoyer">
        </fieldset>
    </form>
</main>