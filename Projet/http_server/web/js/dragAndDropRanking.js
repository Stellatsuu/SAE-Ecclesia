// code d'origine : https://www.javascripttutorial.net/web-apis/javascript-drag-and-drop/
/* draggable elements */
const items = document.querySelectorAll('.item');

for(let item of items){
    item.addEventListener('dragstart', dragStart);
}

function dragStart(e) {
    e.dataTransfer.setData('text/plain', e.target.id);
    setTimeout(() => {
        e.target.classList.add('hide');
    }, 0);
}


/* drop targets */
const boxes = document.querySelectorAll('.box');

boxes.forEach(box => {
    box.addEventListener('dragenter', dragEnter)
    box.addEventListener('dragover', dragOver);
    box.addEventListener('drop', drop);
    box.addEventListener('dragend', dragEnd);
});


function dragEnter(e) {
    e.preventDefault();
}

function dragOver(e) {
    e.preventDefault();
}

function dragEnd(e){
    e.preventDefault();
    setTimeout(() => {
        e.target.classList.remove('hide');
    }, 0);
}

function drop(e) {
    // get the draggable element
    const id = e.dataTransfer.getData('text/plain');
    const draggable = document.getElementById(id);
    let destination = e.target;
    let elementInDestination = null;

    // switch two elements
    if(destination.classList.contains('item')){
        elementInDestination = destination;
        destination = elementInDestination.parentNode;
    }else if(destination.parentElement.classList.contains('item')){
        elementInDestination = destination.parentElement;
        destination = elementInDestination.parentNode;
    }else if(!destination.classList.contains('box')){
        draggable.classList.remove('hide');
        return;
    }

    // add it to the drop target
    if(elementInDestination !== null){
        const origine = draggable.parentNode;
        origine.appendChild(elementInDestination);
        changeValue(origine, elementInDestination);
    }
    destination.appendChild(draggable);

    // display the draggable element
    draggable.classList.remove('hide');

    changeValue(destination, draggable);
}

function changeValue(destination, element){
    // generate the input value
    if(destination.parentElement.classList.contains('source-container')){
        element.querySelector('input').setAttribute("value", "");
    }else{
        const classement = destination.querySelector("span").textContent;
        element.querySelector('input').setAttribute("value", classement);
    }
}