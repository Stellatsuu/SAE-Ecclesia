import { SmartSelect } from "./SmartSelect.js";

const idRedacteurs = redacteurs.map((redacteur) => redacteur.username) || [];
const redacteurs_smart_select = new SmartSelect(
  "redacteurs_input",
  "add_redacteur",
  "redacteurs",
  allUtilisateurOptions,
  idRedacteurs
);

const idVotants = votants.map((votant) => votant.username) || [];
const votants_smart_select = new SmartSelect(
  "votants_input",
  "add_votant",
  "votants",
  allUtilisateurOptions,
  idVotants
);
