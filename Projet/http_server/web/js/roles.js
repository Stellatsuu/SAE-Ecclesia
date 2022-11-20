import { SmartSelect } from "./SmartSelect.js";

const idResponsables =
  question.responsables.map((responsable) => responsable.idUtilisateur) || [];
const responsables_smart_select = new SmartSelect(
  "responsables_input",
  "add_responsable",
  "responsable",
  allUtilisateurOptions,
  idResponsables
);

const idVotants = question.votants.map((votant) => votant.idUtilisateur) || [];
const votants_smart_select = new SmartSelect(
  "votants_input",
  "add_votant",
  "votant",
  allUtilisateurOptions,
  idVotants
);
