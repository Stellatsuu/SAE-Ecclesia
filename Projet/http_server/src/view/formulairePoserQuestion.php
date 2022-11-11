<form method="post" action="frontController.php?controller=question&action=poserQuestion" class="panel">
    <h1>Posez votre question :</h1>
    <fieldset>

        <label for="titre_id">
            Question :
        </label>
        <input readonly type="text" name="titre" id="titre_id" value="<?= htmlspecialchars($question->getTitre()) ?>" required>


        <label for="intitule_id">
            Intitulé :
        </label>
        <textarea rows=6 cols=50 id="intitule_id" name="intitule" required><?= htmlspecialchars($question->getDescription()) ?></textarea>


        <label for="sections_input">Sections:</label>

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
                    <input required type="time" name="heureDebutRedaction" id="" value="16:00">
                </span>
            </div>
            <div>
                <span>
                    au
                    <input required type="date" name="dateFinRedaction" id="" value="<?= $datesFormatees['dateFinRedaction'] ?>">
                </span>
                <span>
                    à
                    <input required type="time" name="heureFinRedaction" id="" value="16:00">
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
                    <input required type="time" name="heureOuvertureVotes" id="" value="16:00">
                </span>
            </div>
            <div>
                <span>
                    au
                    <input required type="date" name="dateFermetureVotes" id="" value="<?= $datesFormatees['dateFermetureVotes'] ?>">
                </span>
                <span>
                    à
                    <input required type="time" name="heureFermetureVotes" id="" value="16:00">
                </span>
            </div>
        </div>


        <input type="hidden" name="idUtilisateur" value="<?= htmlspecialchars($question->getOrganisateur()->getIdUtilisateur()) ?>">
        <input type="hidden" name="idQuestion" value="<?= htmlspecialchars($question->getIdQuestion()) ?>">

    </fieldset>

    <input type="submit" value="Envoyer" />

</form>

<script>
    var sections = 0;
    const sections_input = document.getElementById("sections_input");
    const add_section_button = document.getElementById("add_section");
    const nbSections = document.getElementById("nbSections_id");

    const question = <?= json_encode($question) ?>;
    const sectionsQuestion = question.sections || [];

    sectionsQuestion.forEach(element => {
        const nomSection = element.nom_section;
        const descriptionSection = element.description_section;
        addSection();
        const section = document.getElementById("section" + sections + "_id");
        section.querySelector("input").value = nomSection;
        section.querySelector("textarea").value = descriptionSection;
    });

    if (sectionsQuestion.length == 0) {
        addSection();
    }

    add_section_button.onclick = () => {
        addSection();
        updateNumbers();
    }

    function addSection() {
        sections++;
        var new_section = document.createElement("div");
        new_section.classList.add("conteneur_section");
        new_section.id = "section" + sections + "_id";
        new_section.innerHTML = `
            <div>Section ${sections}<button class="rmbutton" type="button" onclick="removeSection(${sections})">supprimer</button></div>
                
            <label for="nomSection${sections}_id">Nom:</label>
            <input type="text" name="nomSection${sections}" id="nomSection${sections}_id" placeholder="Nom de la section" required>

            <label for="descriptionSection${sections}_id">Description:</label>
            <textarea rows="5" id="descriptionSection${sections}_id" name="descriptionSection${sections}" placeholder="Description de la section" required></textarea>
            `;

        sections_input.insertBefore(new_section, add_section_button);
        updateNumbers();
    }




    function removeSection(i) {
        var section = document.getElementById("section" + i + "_id");
        sections_input.removeChild(section);
        updateNumbers();
    }

    function updateNumbers() {
        const conteneurs_sections = document.getElementsByClassName("conteneur_section");
        for (let i = 0; i < sections.length; i++) {
            sections[i].id = "section" + (i + 1) + "_id";
            sections[i].children[0].innerHTML = `Section ${i + 1}<button class="rmbutton" type="button" onclick="removeSection(${i + 1})">supprimer</button>`;
            sections[i].children[1].id = "nomSection" + (i + 1) + "_id";
            sections[i].children[1].name = "nomSection" + (i + 1);
            sections[i].children[2].id = "descriptionSection" + (i + 1) + "_id";
            sections[i].children[2].name = "descriptionSection" + (i + 1);
        }
        sections = conteneurs_sections.length;
    }
</script>