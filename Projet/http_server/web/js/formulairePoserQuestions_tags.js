const tags_list = document.querySelector('.tags-list');
const input = document.querySelector("#newtag_id")
const button = document.getElementById('add_tag');
var tags = [];

function creerTag(label) {
    const div = document.createElement('div');
    div.setAttribute('class', 'tag');
    const span = document.createElement('span');
    span.innerHTML = label;
    const closeBtn = document.createElement('strong');
    closeBtn.setAttribute('data-item', label);
    closeBtn.innerHTML = 'x';

    closeBtn.addEventListener("click", function(){
        const value = closeBtn.getAttribute('data-item');
        const index = tags.indexOf(value);
        tags = [... tags.slice(0, index), ... tags.slice(index + 1)];
        ajoutTags();
    });

    div.appendChild(span);
    div.appendChild(closeBtn);

    return div;
}
function reset(){
    document.querySelectorAll('.tag').forEach(function (tag){
       tag.parentElement.removeChild(tag);
    });
}
function ajoutTags(){
    reset();
    tags.slice().reverse().forEach(function (tag){
        const ajout = creerTag(tag);
        tags_list.prepend(ajout);
    });
}

button.addEventListener("click", function(){
    if(tags.includes(input.value)){
        document.getElementById("erreur").innerHTML = "Le tag existe déjà.";
    } else if(input.value === ""){
        document.getElementById("erreur").innerHTML = "Le tag est vide.";
    } else{
        document.getElementById("erreur").innerHTML = "";
        tags.push(input.value);
        ajoutTags();
        input.value = '';
    }
});


