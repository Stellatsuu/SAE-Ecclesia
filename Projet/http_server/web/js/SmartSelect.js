export class SmartSelect {
  constructor(inputId, addButtonId, elementName) {
    this.input = document.getElementById(inputId);
    this.addButton = document.getElementById(addButtonId);
    this.elementName = elementName;
    this.nbElements = 0;
    this.allElements = [];

    this.addButton.onclick = () => this.addElement();
  }

  addElement() {
    nbElements++;
    const new_element = document.createElement("span");
    fillElement(new_element, nbElements);
    responsables_input.insertBefore(new_responsable, add_responsable_button);
    this.allElements.push(new_element);
  }

  removeElement(number) {
    const element = document.getElementById(
      `${this.elementName}${number}_select`
    ).parentElement;
    this.input.removeChild(element);
    this.allElements.splice(number - 1, 1);
  }

  fillElement(element, number) {
    element.classList.add(`conteneur_${elementName}`);

    select = document.createElement("select");
    select.name = `${elementName}${number}`;
    select.id = `${elementName}${number}_select`;
    select.onfocus = "this.oldvalue = this.value;";

    select.required = true;
    select.innerHTML = allUtilisateursOption;

    button = document.createElement("button");
    button.type = "button";
    button.classList.add(`remove_${elementName}`);
    button.onclick = () => this.removeElement(number);

    element.appendChild(select);
    element.appendChild(button);
  }

  updateNumbers() {
    this.allElements.forEach((element, index) => {
      element.children[0].id = `${this.elementName}${index + 1}_select`;
      element.children[0].name = `${this.elementName}${index + 1}`;
      element.children[1].onclick = () => this.removeElement(index + 1);
    });
    this.nbElements = this.allElements.length;
  }

  updateRemoveButtonLock() {
    const rmButtons = document.querySelectorAll(`.remove_${this.elementName}`);
    rmButtons.forEach((button) => {
      if (this.nbElements == 1) {
        button.disabled = true;
      } else {
        button.disabled = false;
      }
    });
  }

  unlockValue(value) {
    if (value == "") return;

    const optionsWithValue = document.querySelectorAll(
      `#${this.input.id} option[value="${value}"]`
    );

    optionsWithValue.forEach((option) => {
      option.disabled = false;
    });
  }

  lockValue(caller, value) {
    const optionsWithValue = document.querySelectorAll(
      `#${this.input.id} option[value="${value}"]`
    );

    optionsWithValue.forEach((option) => {
      option.disabled = true;
    });
    caller.querySelector(`option[value="${value}"]`).disabled = value == "";
  }
}
