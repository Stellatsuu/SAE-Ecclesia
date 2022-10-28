<main>
    <form method="post" action="../../../web/frontController.php?action=poserQuestion">
        <fieldset>
            <legend>Posez votre question :</legend>

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


            <h3>Début de la phase de rédaction : </h3>
            le <input type="date" name="dateDebutRedaction" id=""> à <input type="time" name="heureDebutRedaction" id="">

            <h3>Fin de la phase de rédaction : </h3>
            le <input type="date" name="dateFinRedaction" id=""> à
            <input type="time" name="heureFinRedaction" id="">

            <h3>Ouverture des votes : </h3>
            le <input type="date" name="dateOuvertureVotes" id=""> à
            <input type="time" name="heureOuvertureVotes" id="">

            <h3>Fermeture des votes : </h3>
            le <input type="date" name="dateFermetureVotes" id=""> à
            <input type="time" name="heureFermetureVotes" id="">


            <input type="hidden" name="idUtilisateur" value="<?= $question->getOrganisateur()->getIdUtilisateur() ?>">
            <input type="hidden" name="idQuestion" value="<?= $question->getIdQuestion() ?>">


            <input type="submit" value="Envoyer" />

        </fieldset>
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
            new_label.innerHTML = sections + " : ";

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
                labels[i].innerHTML = i + 1 + " : ";
                inputs[i].setAttribute("id", "section_" + i + "_id");
                inputs[i].setAttribute("name", "section_" + i);
                rm_buttons[i].setAttribute("id", "section_" + i + "_rm");
            }
            nbSections.setAttribute("value", sections);
        }
    </script>
</main>