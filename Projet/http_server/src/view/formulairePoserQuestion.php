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
        <textarea rows=6 cols=50 id="description_id" name="description" required><?= htmlspecialchars($question->getDescription()) ?></textarea>


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
        updateNumbers();
    }

    function addSection() {
        nbSections++;
        const new_section = document.createElement("div");
        new_section.classList.add("conteneur_section");
        new_section.id = "section" + nbSections + "_id";
        new_section.innerHTML = `
            <div>Section ${nbSections}<button class="rmbutton" type="button" onclick="removeSection(${nbSections})">supprimer</button></div>
                
            <label for="nomSection${nbSections}_id">Nom:</label>
            <input type="text" name="nomSection${nbSections}" id="nomSection${nbSections}_id" placeholder="Nom de la section" maxlength="50" required>

            <label for="descriptionSection${nbSections}_id">Description:</label>
            <textarea rows="5" id="descriptionSection${nbSections}_id" name="descriptionSection${nbSections}" placeholder="Description de la section" required></textarea>
            `;

        sections_input.insertBefore(new_section, add_section_button);
        updateNumbers();
    }




    function removeSection(i) {
        const section = document.getElementById("section" + i + "_id");
        sections_input.removeChild(section);
        updateNumbers();
    }

    function updateNumbers() {
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