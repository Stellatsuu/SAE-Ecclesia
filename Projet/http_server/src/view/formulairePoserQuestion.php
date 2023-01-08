<form method="post" action="frontController.php?controller=question&action=poserQuestion" class="panel">
    <h1>Posez votre question :</h1>
    <fieldset>

        <label for="titre_id">
            Question :
        </label>
        <input readonly type="text" name="titre" id="titre_id" value="<?= htmlspecialchars($question->getTitre()) ?>" required>


        <label for="description_id">
            Description :
        </label>

        <div class="text_input_div">
            <textarea rows=6 cols=50 id="description_id" name="description" maxlength="4000" required><?= htmlspecialchars($question->getDescription()) ?></textarea>
            <span class="indicateur_max_chars unselectable">4000 max</span>
        </div>

        <label>Plan :</label>
        <div id="sections_input">
            <button type="button" id="add_section">+</button>
        </div>

        <label>Phase de rédaction : </label>
        <div class="conteneurDateHeure">
            <div>
                <span>
                    Du
                    <input required type="date" name="dateDebutRedaction" value="<?= $datesFormatees['dateDebutRedaction'] ?>">
                </span>
                <span>
                    à
                    <input required type="time" name="heureDebutRedaction" value="<?= $heuresFormatees['heureDebutRedaction'] ?>">
                </span>
            </div>
            <div>
                <span>
                    au
                    <input required type="date" name="dateFinRedaction" value="<?= $datesFormatees['dateFinRedaction'] ?>">
                </span>
                <span>
                    à
                    <input required type="time" name="heureFinRedaction" value="<?= $heuresFormatees['heureFinRedaction'] ?>">
                </span>
            </div>
        </div>

        <label>Phase de votes : </label>
        <div class="conteneurDateHeure">
            <div>
                <span>
                    Du
                    <input required type="date" name="dateOuvertureVotes" value="<?= $datesFormatees['dateOuvertureVotes'] ?>">
                </span>
                <span>
                    à
                    <input required type="time" name="heureOuvertureVotes" value="<?= $heuresFormatees['heureOuvertureVotes'] ?>">
                </span>
            </div>
            <div>
                <span>
                    au
                    <input required type="date" name="dateFermetureVotes" value="<?= $datesFormatees['dateFermetureVotes'] ?>">
                </span>
                <span>
                    à
                    <input required type="time" name="heureFermetureVotes" value="<?= $heuresFormatees['heureFermetureVotes'] ?>">
                </span>
            </div>
        </div>

        <div id="roles_input">
            <div id="redacteurs_input">
                <label>Rédacteurs : </label>
                <button type="button" id="add_redacteur">+</button>
            </div>
            <div id="votants_input">
                <label>Votants : </label>
                <button type="button" id="add_votant">+</button>
            </div>
        </div>

        <div id="systeme_vote_input">
            <label for="systeme_vote_id">Système de vote :</label>
            <select name="systeme_vote" id="systeme_vote_id">
                <option value="majoritaire_a_un_tour">Majoritaire à un tour</option>
                <option value="approbation">Vote par approbation</option>
                <option value="alternatif">Vote alternatif</option>
                <option value="jugement_majoritaire">Jugement majoritaire</option>
            </select>
        </div>

        <div id="tags_input">
            <label for="tags_id">Tag(s) :</label>
            <input type="text" id="tags_id" name="tags" placeholder="Veuillez séparer les tags avec des espaces." pattern="[a-zA-Z0-9-\s]*">

        </div>

        <input type="hidden" name="idQuestion" value="<?= htmlspecialchars($question->getIdQuestion()) ?>">

    </fieldset>

    <input type="submit" value="Envoyer" />

</form>

<script>
    const question = <?= json_encode($question) ?>;
    const utilisateurs = <?= json_encode($utilisateurs) ?>;
    const allUtilisateurOptions = '<option value="" selected disabled>---<\/option>' + utilisateurs.map(utilisateur => `<option value="${utilisateur.username}">${utilisateur.nomUsuel}<\/option>`).join("\n");
</script>

<script src="js/formulairePoserQuestion_sections.js"></script>
<script type='module' src="js/formulairePoserQuestion_roles.js"></script>