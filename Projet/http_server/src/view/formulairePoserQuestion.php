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

        <label for="sections_input">Plan :</label>
        <div id="sections_input">
            <button type="button" id="add_section">+</button>
        </div>


        <label>Phase de rédaction : </label>
        <div class="conteneurDateHeure">
            <div>
                <span>
                    Du
                    <input required type="date" name="dateDebutRedaction" id="" value="<?= $datesFormatees['dateDebutRedaction'] ?>">
                </span>
                <span>
                    à
                    <input required type="time" name="heureDebutRedaction" id="" value="<?= $heuresFormatees['heureDebutRedaction'] ?>">
                </span>
            </div>
            <div>
                <span>
                    au
                    <input required type="date" name="dateFinRedaction" id="" value="<?= $datesFormatees['dateFinRedaction'] ?>">
                </span>
                <span>
                    à
                    <input required type="time" name="heureFinRedaction" id="" value="<?= $heuresFormatees['heureFinRedaction'] ?>">
                </span>
            </div>
        </div>

        <label>Phase de votes : </label>
        <div class="conteneurDateHeure">
            <div>
                <span>
                    Du
                    <input required type="date" name="dateOuvertureVotes" id="" value="<?= $datesFormatees['dateOuvertureVotes'] ?>">
                </span>
                <span>
                    à
                    <input required type="time" name="heureOuvertureVotes" id="" value="<?= $heuresFormatees['heureOuvertureVotes'] ?>">
                </span>
            </div>
            <div>
                <span>
                    au
                    <input required type="date" name="dateFermetureVotes" id="" value="<?= $datesFormatees['dateFermetureVotes'] ?>">
                </span>
                <span>
                    à
                    <input required type="time" name="heureFermetureVotes" id="" value="<?= $heuresFormatees['heureFermetureVotes'] ?>">
                </span>
            </div>
        </div>

        <div id="roles_input">

            <div id="responsables_input">
                <label for="responsables_input">Rédacteurs : </label>


                <button type="button" id="add_responsable">+</button>
            </div>


            <div id="votants_input">
                <label for="votants_input">Votants : </label>
                <button type="button" id="add_votant">+</button>
            </div>

        </div>

        <input type="hidden" name="idUtilisateur" value="<?= htmlspecialchars($question->getOrganisateur()->getIdUtilisateur()) ?>">
        <input type="hidden" name="idQuestion" value="<?= htmlspecialchars($question->getIdQuestion()) ?>">

    </fieldset>

    <input type="submit" value="Envoyer" />

</form>

<script>
    const question = <?= json_encode($question) ?>;
    const utilisateurs = <?= json_encode($utilisateurs) ?>;
    console.log(utilisateurs);
    const allUtilisateurOptions = '<option value="" selected disabled>---</option>' + utilisateurs.map(utilisateur => `<option value="${utilisateur.idUtilisateur}">${(utilisateur.nom).toUpperCase()} ${utilisateur.prenom}</option>`).join("\n");
</script>

<script src="js/sections.js"></script>
<script type='module' src="js/roles.js"></script>
