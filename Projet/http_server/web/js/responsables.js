var nbResponsables = 0;
const responsables_input = document.getElementById("responsables_input");
const add_responsable_button = document.getElementById("add_responsable");

const idResponsables = question.responsables.map((responsable) => responsable.idUtilisateur);

idResponsables.forEach((id) => {
  addResponsable();
  const select = document.getElementById("responsable" + nbResponsables + "_select");
  select.value = id;
  lockValueResponsable(select, id);
});

if(nbResponsables == 0) addResponsable();

add_responsable_button.onclick = () => {
  addResponsable();
};

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
  updateRemoveResponsableButtonLock();
}

function removeResponsable(i) {
  const responsable = document.getElementById(
    "responsable" + i + "_select"
  ).parentElement;

  const selectValue = responsable.children[0].value;
  unlockValueResponsable(selectValue);

  responsables_input.removeChild(responsable);
  updateResponsablesNumbers();
  updateRemoveResponsableButtonLock();
}

function updateRemoveResponsableButtonLock() {
  const rmResponsableButtons = document.querySelectorAll(".remove_responsable");
  rmResponsableButtons.forEach((button) => {
    if (nbResponsables == 1) {
      button.disabled = true;
    } else {
      button.disabled = false;
    }
  });
}

function updateResponsablesNumbers() {
  const conteneurs_responsables = document.getElementsByClassName(
    "conteneur_responsable"
  );
  for (let i = 0; i < conteneurs_responsables.length; i++) {
    conteneurs_responsables[i].children[0].id =
      "responsable" + (i + 1) + "_select";
    conteneurs_responsables[i].children[0].name = "responsable" + (i + 1);
    conteneurs_responsables[i].children[1].onclick = () =>
      removeResponsable(i + 1);
  }
  nbResponsables = conteneurs_responsables.length;
}

function unlockValueResponsable(value) {
  if (value == "") return;

  const options = document.querySelectorAll(
    `#responsables_input option[value="${value}"]`
  );
  options.forEach((option) => (option.disabled = false));
}

function lockValueResponsable(caller, value) {
  const options = document.querySelectorAll(
    `#responsables_input option[value="${value}"]`
  );
  options.forEach((option) => (option.disabled = true));
  caller.querySelector(`option[value="${value}"]`).disabled = value == "";
}
