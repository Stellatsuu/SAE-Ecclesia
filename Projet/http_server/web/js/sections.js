var nbSections = 0;
const sections_input = document.getElementById("sections_input");
const add_section_button = document.getElementById("add_section");

const sectionsQuestion = question.sections || [];

sectionsQuestion.forEach((element) => {
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
};

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
        conteneurs_sections[i].children[0].innerHTML = `Section ${i + 1}<button class="rmbutton" type="button" onclick="removeSection(${i + 1})">supprimer</button>`;
        conteneurs_sections[i].children[1].id = "nomSection" + (i + 1) + "_id";
        conteneurs_sections[i].children[1].name = "nomSection" + (i + 1);
        conteneurs_sections[i].children[2].children[0].id = "nomSection" + (i + 1) + "_id";
        conteneurs_sections[i].children[2].children[0].name = "nomSection" + (i + 1);
        conteneurs_sections[i].children[3].id = "descriptionSection" + (i + 1) + "_id";
        conteneurs_sections[i].children[3].name = "descriptionSection" + (i + 1);
        conteneurs_sections[i].children[4].children[0].id = "descriptionSection" + (i + 1) + "_id";
        conteneurs_sections[i].children[4].children[0].name = "descriptionSection" + (i + 1);
        conteneurs_sections[i].id = "section" + (i + 1) + "_id";
    }
    nbSections = conteneurs_sections.length;
}
