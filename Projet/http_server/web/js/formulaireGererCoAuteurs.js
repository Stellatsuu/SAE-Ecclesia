import { SmartSelect } from "./SmartSelect.js";

const idCoAuteurs = coAuteurs.map((coAuteur) => coAuteur.username) || [];

const co_auteurs_smart_select = new SmartSelect("co_auteurs_input", "add_co_auteur", "co_auteur", options, idCoAuteurs, false);