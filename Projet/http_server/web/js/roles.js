import { SmartSelect } from "./SmartSelect.js";

const idRedacteurs =
  question.redacteurs.map((redacteur) => redacteur.idUtilisateur) || [];
const redacteurs_smart_select = new SmartSelect(
  "redacteurs_input",
  "add_redacteur",
  "redacteur",
  allUtilisateurOptions,
  idRedacteurs
);

const idVotants = question.votants.map((votant) => votant.idUtilisateur) || [];
const votants_smart_select = new SmartSelect(
  "votants_input",
  "add_votant",
  "votant",
  allUtilisateurOptions,
  idVotants
);
