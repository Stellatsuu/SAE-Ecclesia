import { SmartSelect } from "./SmartSelect.js";

const idRedacteurs = redacteurs || [];
const redacteurs_smart_select = new SmartSelect(
  "redacteurs_input",
  "add_redacteur",
  "redacteurs",
  allUtilisateurOptions,
  idRedacteurs
);

const idVotants = votants || [];
const votants_smart_select = new SmartSelect(
  "votants_input",
  "add_votant",
  "votants",
  allUtilisateurOptions,
  idVotants
);
