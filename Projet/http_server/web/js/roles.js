import { SmartSelect } from "./SmartSelect.js";

const idRedacteurs =
  question.redacteurs.map((redacteur) => redacteur.username) || [];
const redacteurs_smart_select = new SmartSelect(
  "redacteurs_input",
  "add_redacteur",
  "redacteur",
  allUtilisateurOptions,
  idRedacteurs
);

const idVotants = question.votants.map((votant) => votant.username) || [];
const votants_smart_select = new SmartSelect(
  "votants_input",
  "add_votant",
  "votant",
  allUtilisateurOptions,
  idVotants
);
