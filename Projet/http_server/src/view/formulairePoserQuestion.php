<main>
    <form method="post" action="frontController.php?action=poserQuestion" class="panel">
        <h1>Posez votre question :</h1>
        <fieldset>

            <label for="titre_id">
                Question :
            </label>
            <textarea readonly rows=6 cols=50 id="titre_id" name="titre" required><?= $question->getTitre() ?></textarea>


            <label for="intitule_id">
                Intitulé :
            </label>
            <textarea rows=6 cols=50 id="intitule_id" name="intitule" required><?= $question->getIntitule() ?></textarea>


            <label for="sections_input">Sections:</label>

            <div id="sections_input">
                <button type="button" id="add_section">+</button>
                <input hidden id="nbSections_id" type="number" value="0" name="nbSections">
            </div>


            <label>Phase de rédaction : </label>
            <span class="conteneurDateHeure">Du <input required type="date" name="dateDebutRedaction" id=""> à
            <input required type="time" name="heureDebutRedaction" id="">

            au <input required type="date" name="dateFinRedaction" id=""> à
                <input required type="time" name="heureFinRedaction" id=""></span>

            <label>Phase de votes : </label>
            <span class="conteneurDateHeure">Du <input required type="date" name="dateOuvertureVotes" id=""> à
            <input required type="time" name="heureOuvertureVotes" id="">


            au <input required type="date" name="dateFermetureVotes" id=""> à
                <input required type="time" name="heureFermetureVotes" id=""></span>


            <input type="hidden" name="idUtilisateur" value="<?= $question->getOrganisateur()->getIdUtilisateur() ?>">
            <input type="hidden" name="idQuestion" value="<?= $question->getIdQuestion() ?>">

        </fieldset>

            <input type="submit" value="Envoyer" />


    </form>

    <script>
        var sections = 0;
        var sections_input = document.getElementById("sections_input");
        var add_section_button = document.getElementById("add_section");
        var nbSections = document.getElementById("nbSections_id");

        add_section_button.onclick = () => {
            addSection();
            updateNumbers();
        }

        function addSection() {
            sections++;
            var new_section = document.createElement("div");

            var new_label = document.createElement("label");
            new_label.setAttribute("for", "section_" + sections + "_id");
            //new_label.innerHTML = sections + " : ";

            var new_input = document.createElement("input");
            new_input.setAttribute("type", "text");
            new_input.setAttribute("id", "section_" + sections + "_id");
            new_input.setAttribute("name", "section_" + sections);
            new_input.setAttribute("required", "true");

            var new_rmbutton = document.createElement("button");
            new_rmbutton.setAttribute("type", "button");
            new_rmbutton.setAttribute("class", "rmbutton");
            new_rmbutton.setAttribute("id", "section_" + sections + "_rm");
            new_rmbutton.innerHTML = "-";
            new_rmbutton.onclick = () => {
                var idSection = new_rmbutton.getAttribute("id").split("_")[1];
                removeSection(idSection);
            }

            new_section.appendChild(new_label);
            new_section.appendChild(new_input);
            new_section.appendChild(new_rmbutton);

            sections_input.insertBefore(new_section, add_section_button);
        }

        function removeSection(i) {
            sections_input.removeChild(document.getElementById("section_" + i + "_id").parentNode);
            sections--;
            updateNumbers();
        }

        function updateNumbers() {
            var labels = sections_input.getElementsByTagName("label");
            var inputs = sections_input.getElementsByTagName("input");
            var rm_buttons = sections_input.getElementsByClassName("rmbutton");

            for (var i = 0; i < labels.length; i++) {
                //labels[i].innerHTML = i + 1 + " : ";
                inputs[i].setAttribute("id", "section_" + i + "_id");
                inputs[i].setAttribute("name", "section_" + i);
                rm_buttons[i].setAttribute("id", "section_" + i + "_rm");
            }
            nbSections.setAttribute("value", sections);
        }
    </script>
</main>