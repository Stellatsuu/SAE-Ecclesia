export class SmartSelect {
  constructor(inputId, addButtonId, elementName, allOptions, defaultValues, preventRemoveLast = true) {
    this.input = document.getElementById(inputId);
    this.addButton = document.getElementById(addButtonId);
    this.elementName = elementName;
    this.nbElements = 0;
    this.allElements = [];
    this.allOptions = allOptions;
    this.addButton.onclick = () => this.addElement();
    this.preventRemoveLast = preventRemoveLast;

    if (defaultValues) {
      defaultValues.forEach((id) => {
        const select = this.addElement().children[0];
        select.value = id;
        this.lockValue(select, id);
      });
    }

    if (this.nbElements == 0) this.addElement();
  }

  addElement() {
    this.nbElements++;
    const new_element = document.createElement("span");
    this.fillElement(new_element, this.nbElements);
    this.input.insertBefore(new_element, this.addButton);
    this.allElements.push(new_element);

    for (let i = 0; i < this.allElements.length; i++) {
      const select = this.allElements[i].children[0];
      this.lockValue(select, select.value);
    }

    this.updateNumbers();
    this.updateRemoveButtonLock();
    return new_element;
  }

  removeElement(number) {
    const element = document.getElementById(
      `${this.elementName}${number}_select`
    ).parentElement;

    this.unlockValue(element.children[0].value);

    this.input.removeChild(element);
    this.allElements.splice(number - 1, 1);
    this.updateNumbers();
    this.updateRemoveButtonLock();
  }

  fillElement(element, number) {
    element.classList.add(`conteneur_${this.elementName}`);

    const select = document.createElement("select");
    select.name = `${this.elementName}${number}`;
    select.id = `${this.elementName}${number}_select`;
    select.required = true;
    select.innerHTML = this.allOptions;
    select.onfocus = "this.oldvalue = this.value;";
    select.onchange = (event) => {
      this.lockValue(event.target, event.target.value);
      this.unlockValue(event.target.oldvalue);
    };

    const button = document.createElement("button");
    button.type = "button";
    button.classList.add(`remove_${this.elementName}`);
    button.innerHTML = "-";
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
      if (this.nbElements == 1 && this.preventRemoveLast) {
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
