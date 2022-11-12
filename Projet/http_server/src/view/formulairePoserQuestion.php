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
                <label for="responsables_input">Responsables : </label>


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
    var nbSections = 0;
    const sections_input = document.getElementById("sections_input");
    const add_section_button = document.getElementById("add_section");

    const question = <?= json_encode($question) ?>;
    const sectionsQuestion = question.nbSections || [];

    sectionsQuestion.forEach(element => {
        const nomSection = element.nom_section;
        const descriptionSection = element.description_section;
        addSection();
        const section = document.getElementById("section" + nbSections + "_id");
        section.querySelector("input").value = nomSection;
        section.querySelector("textarea").value = descriptionSection;
    });

    if (sectionsQuestion.length == 0) {
        addSection();
    }

    add_section_button.onclick = () => {
        addSection();
        updateSectionsNumbers();
    }

    function addSection() {
        nbSections++;
        const new_section = document.createElement("div");
        new_section.classList.add("conteneur_section");
        new_section.id = "section" + nbSections + "_id";
        new_section.innerHTML = `
            <div>Section ${nbSections}<button class="rmbutton" type="button" onclick="removeSection(${nbSections})">supprimer</button></div>
            <label for="nomSection${nbSections}_id">Nom:</label>
            <div class="text_input_div">   
                <input type="text" name="nomSection${nbSections}" id="nomSection${nbSections}_id" placeholder="Nom de la section" maxlength="50" required>
                <span class="indicateur_max_chars">50 max</span>
            </div>

            <label for="descriptionSection${nbSections}_id">Description:</label>
            <div class="text_input_div">
                <textarea rows="5" id="descriptionSection${nbSections}_id" name="descriptionSection${nbSections}" maxlength="2000" placeholder="Description de la section" required></textarea>
                <span class="indicateur_max_chars">2000 max</span>
            </div>`;

        sections_input.insertBefore(new_section, add_section_button);
        updateSectionsNumbers();
    }

    function removeSection(i) {
        const section = document.getElementById("section" + i + "_id");
        sections_input.removeChild(section);
        updateSectionsNumbers();
    }

    function updateSectionsNumbers() {
        const conteneurs_sections = document.getElementsByClassName("conteneur_section");
        for (let i = 0; i < conteneurs_sections.length; i++) {
            conteneurs_sections[i].id = "section" + (i + 1) + "_id";
            conteneurs_sections[i].children[0].innerHTML = `Section ${i + 1}<button class="rmbutton" type="button" onclick="removeSection(${i + 1})">supprimer</button>`;
            conteneurs_sections[i].children[1].id = "nomSection" + (i + 1) + "_id";
            conteneurs_sections[i].children[1].name = "nomSection" + (i + 1);
            conteneurs_sections[i].children[2].id = "descriptionSection" + (i + 1) + "_id";
            conteneurs_sections[i].children[2].name = "descriptionSection" + (i + 1);
        }
        nbSections = conteneurs_sections.length;


    }
</script>

<script>
    var nbResponsables = 0;
    const responsables_input = document.getElementById("responsables_input");
    const add_responsable_button = document.getElementById("add_responsable");

    const utilisateurs = <?= json_encode($utilisateurs) ?>;
    const allUtilisateursOption = '<option value="" selected disabled>---</option>' + utilisateurs.map(utilisateur => `<option value="${utilisateur.idUtilisateur}">${(utilisateur.nom).toUpperCase()} ${utilisateur.prenom}</option>`).join("\n");

    add_responsable_button.onclick = () => {
        addResponsable();
        updateResponsablesNumbers();
    }

    addResponsable();

    function addResponsable() {
        nbResponsables++;
        const new_responsable = document.createElement("span");
        new_responsable.classList.add("conteneur_responsable");
        new_responsable.innerHTML = `
            <select name="responsable${nbResponsables}" id="responsable${nbResponsables}_select" onfocus="this.oldvalue = this.value;" onchange="unlockValueResponsable(this.oldvalue);lockValueResponsable(this, this.value)" required>
                ${allUtilisateursOption}
            </select>
            <button type="button" class="remove_responsable" onclick="removeResponsable(${nbResponsables})">-</button>`;

        responsables_input.insertBefore(new_responsable, add_responsable_button);

        const responsables = document.getElementsByClassName("conteneur_responsable");
        for (let i = 0; i < responsables.length; i++) {
            const select = responsables[i].children[0];
            lockValueResponsable(select, select.value);
        }

        updateResponsablesNumbers();
    }

    function removeResponsable(i) {
        const responsable = document.getElementById("responsable" + i + "_select").parentElement;

        const selectValue = responsable.children[0].value;
        unlockValueResponsable(selectValue);

        responsables_input.removeChild(responsable);
        updateResponsablesNumbers();
    }

    function updateResponsablesNumbers() {
        const conteneurs_responsables = document.getElementsByClassName("conteneur_responsable");
        for (let i = 0; i < conteneurs_responsables.length; i++) {
            conteneurs_responsables[i].children[0].id = "responsable" + (i + 1) + "_select";
            conteneurs_responsables[i].children[0].name = "responsable" + (i + 1);
            conteneurs_responsables[i].children[1].onclick = () => removeResponsable(i + 1);
        }
        nbResponsables = conteneurs_responsables.length;
    }

    function unlockValueResponsable(value) {
        if(value == "") return;

        const options = document.querySelectorAll(`#responsables_input option[value="${value}"]`);
        options.forEach(option => option.disabled = false);
    }

    function lockValueResponsable(caller, value) {
        const options = document.querySelectorAll(`#responsables_input option[value="${value}"]`);
        options.forEach(option => option.disabled = true);
        caller.querySelector(`option[value="${value}"]`).disabled = (value == "");
    }

</script>

<script>
    var nbVotants = 0;
    const votants_input = document.getElementById("votants_input");
    const add_votant_button = document.getElementById("add_votant");

    add_votant_button.onclick = () => {
        addVotant();
        updateVotantsNumbers();
    }

    addVotant();

    function addVotant() {
        nbVotants++;
        const new_votant = document.createElement("span");
        new_votant.classList.add("conteneur_votant");
        new_votant.innerHTML = `
            <select name="votant${nbVotants}" id="votant${nbVotants}_select" onfocus="this.oldvalue = this.value;" onchange="unlockValueVotant(this.oldvalue);lockValueVotant(this, this.value)" required>
                ${allUtilisateursOption}
            </select>
            <button type="button" class="remove_votant" onclick="removeVotant(${nbVotants})">-</button>`;

        votants_input.insertBefore(new_votant, add_votant_button);

        const votants = document.getElementsByClassName("conteneur_votant");
        for (let i = 0; i < votants.length; i++) {
            const select = votants[i].children[0];
            lockValueVotant(select, select.value);
        }

        updateVotantsNumbers();
    }

    function removeVotant(i) {
        const votant = document.getElementById("votant" + i + "_select").parentElement;

        unlockValueVotant(votant.children[0].value);

        votants_input.removeChild(votant);
        updateVotantsNumbers();
    }

    function updateVotantsNumbers() {
        const conteneurs_votants = document.getElementsByClassName("conteneur_votant");
        for (let i = 0; i < conteneurs_votants.length; i++) {
            conteneurs_votants[i].children[0].id = "votant" + (i + 1) + "_select";
            conteneurs_votants[i].children[0].name = "votant" + (i + 1);
            conteneurs_votants[i].children[1].onclick = () => removeVotant(i + 1);
        }
        nbVotants = conteneurs_votants.length;
    }

    function unlockValueVotant(value) {
        const options = document.querySelectorAll(`#votants_input option[value="${value}"]`);
        options.forEach(option => option.disabled = false);
    }

    function lockValueVotant(caller, value) {
        const options = document.querySelectorAll(`#votants_input option[value="${value}"]`);
        options.forEach(option => option.disabled = true);
        caller.querySelector(`option[value="${value}"]`).disabled = false;
    }

</script>