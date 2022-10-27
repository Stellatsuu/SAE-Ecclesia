<main>
    <form method="post" action="../../../web/frontController.php?action=poserQuestion">
        <fieldset>
            <legend>Posez votre question :</legend>
            <p>
                <label for="titre_id">
                    <h3>Question :</h3>
                </label>
                <textarea readonly rows=6 cols=50 id="titre_id" name="titre" required><?=$question->getTitre()?></textarea>
            </p>
            <p>
                <label for="intitule_id">
                    <h3>Intitulé :</h3>
                </label>
                <textarea rows=6 cols=50 id="intitule_id" name="intitule" required><?=$question->getIntitule()?></textarea>
            </p>

            <div id="sections_input">
                <h3>Sections:</h3>

                <span id="add_section">+</span>
                <input id="nbSections_id" type="hidden" name="nbSections">
            </div>

            <p>
            <h3>Ouverture aux propositions : </h3>
            le <input type="date" name="dateDebutRedaction" id=""> à
            <input type="time" name="heureDebutRedaction" id="">
            </p>
            <p>
            <h3>Fermeture des propositions : </h3>
            le <input type="date" name="dateFinRedaction" id=""> à
            <input type="time" name="heureFinRedaction" id="">
            </p>

            <p>
            <h3>Ouverture des votes : </h3>
            le <input type="date" name="dateOuvertureVotes" id=""> à
            <input type="time" name="heureOuvertureVotes" id="">
            </p>

            <p>
            <h3>Fermetures des propositions : </h3>
            le <input type="date" name="dateFermetureVotes" id=""> à
            <input type="time" name="heureFermetureVotes" id="">
            </p>

            <input type="hidden" name="idUtilisateur" value="<?=$question->getOrganisateur()->getIdUtilisateur()?>">
            <input type="hidden" name="idQuestion" value="<?=$question->getIdQuestion()?>">

            <p>
                <input type="submit" value="Envoyer" />
            </p>
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
            var new_section = document.createElement("p");

            var new_label = document.createElement("label");
            new_label.setAttribute("for", "section_" + sections + "_id");
            new_label.innerHTML = sections + " : ";

            var new_input = document.createElement("input");
            new_input.setAttribute("type", "text");
            new_input.setAttribute("id", "section_" + sections + "_id");
            new_input.setAttribute("name", "section_" + sections);

            var new_rmbutton = document.createElement("span");
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