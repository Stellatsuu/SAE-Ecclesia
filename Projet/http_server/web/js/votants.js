var nbVotants = 0;
const votants_input = document.getElementById("votants_input");
const add_votant_button = document.getElementById("add_votant");

const idVotants = question.votants.map((votant) => votant.idUtilisateur);

idVotants.forEach((idVotant) => {
  addVotant();
  const select = document.getElementById("votant" + nbVotants + "_select");
  select.value = idVotant;
  lockValueVotant(select, idVotant);
});

if (nbVotants == 0) addVotant();

add_votant_button.onclick = () => {
  addVotant();
  updateVotantsNumbers();
};

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
  const votant = document.getElementById(
    "votant" + i + "_select"
  ).parentElement;

  unlockValueVotant(votant.children[0].value);

  votants_input.removeChild(votant);
  updateVotantsNumbers();
}

function updateVotantsNumbers() {
  const conteneurs_votants =
    document.getElementsByClassName("conteneur_votant");
  for (let i = 0; i < conteneurs_votants.length; i++) {
    conteneurs_votants[i].children[0].id = "votant" + (i + 1) + "_select";
    conteneurs_votants[i].children[0].name = "votant" + (i + 1);
    conteneurs_votants[i].children[1].onclick = () => removeVotant(i + 1);
  }
  nbVotants = conteneurs_votants.length;
}

function unlockValueVotant(value) {
  const options = document.querySelectorAll(
    `#votants_input option[value="${value}"]`
  );
  options.forEach((option) => (option.disabled = false));
}

function lockValueVotant(caller, value) {
  const options = document.querySelectorAll(
    `#votants_input option[value="${value}"]`
  );
  options.forEach((option) => (option.disabled = true));
  caller.querySelector(`option[value="${value}"]`).disabled = false;
}
