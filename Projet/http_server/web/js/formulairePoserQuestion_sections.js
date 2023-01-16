var nbSections = 0;
const sections_input = document.getElementById("sections_input");
const add_section_button = document.getElementById("add_section");

sections.forEach((element) => {
  const nomSection = element.titre;
  const descriptionSection = element.description;
  addSection();
  const section = document.getElementById("section" + nbSections + "_id");
  section.querySelector("input").value = nomSection;
  section.querySelector("textarea").value = descriptionSection;
});

if (sections.length == 0) {
  addSection();
}

add_section_button.onclick = () => {
  addSection();
};

function addSection() {
  nbSections++;
  const new_section = document.createElement("div");
  new_section.classList.add("conteneur_section");
  new_section.id = "section" + nbSections + "_id";
  new_section.innerHTML = `
        <div>
          <span>Section ${nbSections}</span>
          <button class="remove_section" type="button" onclick="removeSection(${nbSections})">supprimer</button>
        </div>

        <label for="nomSection${nbSections}_id">Nom:</label>

        <div class="text_input_div">   
            <input type="text" name="sections[${nbSections}][titre]" id="nomSection${nbSections}_id" placeholder="Nom de la section" maxlength="50" required>
            <span class="indicateur_max_chars">50 max</span>
        </div>

        <label for="descriptionSection${nbSections}_id">Description:</label>
        <div class="text_input_div">
            <textarea rows="5" id="descriptionSection${nbSections}_id" name="sections[${nbSections}][description]" maxlength="2000" placeholder="Description de la section" required></textarea>
            <span class="indicateur_max_chars">2000 max</span>
        </div>`;

  sections_input.insertBefore(new_section, add_section_button);
  updateSectionsNumbers();
  updateRemoveSectionButtonLock();
}

function removeSection(i) {
  const section = document.getElementById("section" + i + "_id");
  sections_input.removeChild(section);
  updateSectionsNumbers();
  updateRemoveSectionButtonLock();
}

function updateRemoveSectionButtonLock() {
  const rmSectionButtons = document.querySelectorAll(".remove_section");
  rmSectionButtons.forEach((button) => {
    if (nbSections == 1) {
      button.disabled = true;
    } else {
      button.disabled = false;
    }
  });
}

function updateSectionsNumbers() {
  const conteneurs_sections =
    document.getElementsByClassName("conteneur_section");

  for (let i = 0; i < conteneurs_sections.length; i++) {

    conteneurs_sections[i].children[0].innerHTML = `
    <span>Section ${i + 1}</span>
    <button class="remove_section" type="button" onclick="removeSection(${i + 1})">supprimer</button>`;


    conteneurs_sections[i].children[1] = `
    <label for="nomSection${i + 1}_id">Nom:</label>`;

    let text_input_div1 = conteneurs_sections[i].children[2];
    text_input_div1.children[0].id = `nomSection${i + 1}_id`;
    text_input_div1.children[0].name = `sections[${i + 1}][titre]`;

    conteneurs_sections[i].children[3] = `
    <label for="descriptionSection${i + 1}_id">Description:</label>`;

    let text_input_div2 = conteneurs_sections[i].children[4];
    text_input_div2.children[0].id = `descriptionSection${i + 1}_id`;
    text_input_div2.children[0].name = `sections[${i + 1}][description]`;

    conteneurs_sections[i].id = `section${i + 1}_id`;
  }
  nbSections = conteneurs_sections.length;
}
