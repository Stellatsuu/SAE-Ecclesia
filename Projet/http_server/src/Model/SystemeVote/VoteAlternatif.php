<?php

namespace App\SAE\Model\SystemeVote{

    use App\SAE\Controller\VoteController;
    use App\SAE\Lib\ConnexionUtilisateur;
    use App\SAE\Model\DataObject\Proposition;
    use App\SAE\Model\DataObject\Vote;
    use App\SAE\Model\Repository\PropositionRepository;
    use App\SAE\Model\Repository\VotantRepository;
    use App\SAE\Model\Repository\VoteRepository;
    use App\SAE\Model\SystemeVote\VoteAlternatif\VoteUtilisateur;

    enum StatusProposition{
        case perdante;
        case normale;
        case gagnante;
    }

    class VoteAlternatif extends AbstractSystemeVote{
        public function getNom(): string
        {
            return "alternatif";
        }

        public function getNomComplet(): string
        {
            return "vote alternatif";
        }

        public function afficherInterfaceVote(): string
        {
            $username = ConnexionUtilisateur::getUsernameSiConnecte();
            $question = $this->getQuestion();
            $propositions = (new PropositionRepository)->selectAllByQuestion($question->getIdQuestion());
            $nbPropositions = count($propositions);
            $aDejaVote = (new VoteRepository)->existsForQuestion($question->getIdQuestion(), $username);
            if ($aDejaVote) {
                $votes = (new VoteRepository)->selectAllByQuestionEtVotant($question->getIdQuestion(), $username);
            }

            $propositionCheckboxes = [];
            for ($i = 0; $i < count($propositions); $i++) {
                $p = $propositions[$i];
                $idProposition = rawurlencode($p->getIdProposition());
                $titreProposition = htmlspecialchars($p->getTitreProposition());

                $valeur = "";
                if($aDejaVote){
                    foreach($votes as $vote){
                        if($vote->getIdProposition() == $idProposition){
                            $valeur = $vote->getValeur();
                            break;
                        }
                    }
                }

                $input = <<<HTML
                <label for="choix$idProposition">$titreProposition</label>
                <input type="number" id="choix$idProposition" name="choix$idProposition" value="$valeur" min="1" max="$nbPropositions" required>
            HTML;

                $propositionInput[] = $input;
            }

            $propositionInput = implode("", $propositionInput);
            $submit = $aDejaVote ? "Modifier mon vote" : "Voter";

            $res = <<<HTML
        <p>Le vote se déroule en 1 tour. Classez les propositions suivantes par ordre de préférence.</p>
        <div class="choix-proposition">
            $propositionInput
        </div>
        <input type="submit" value="$submit">
        HTML;

            return $res;
        }

        public function afficherResultats(): string{
            $propositions = (new PropositionRepository())->selectAllByQuestion($this->getQuestion()->getIdQuestion());
            $resultats = $this->calculerResultats();


            $header = "<td>Candidats</td>";
            for($i = 0; $i < count($resultats); $i++){
                $header .= "<td>" . ($i+1) . ($i==0 ? "er" : "eme") . " tour</td>";
            }

            $propositionGagnante = "";
            $votesParProposition = "";
            foreach($propositions as $proposition){
                $idProposition = $proposition->getidProposition();
                $ligne = "<tr><td>{$proposition->getTitreProposition()}</td>";
                $pourcentagePrecedent = 0;
                $estPremiereColonne = true;

                foreach($resultats as $tour){
                    $cellule = "<td>";

                    foreach($tour as $idPropositionTour => $value){
                        $pourcentage = $value[0];
                        $status = $value[1];

                        if($idPropositionTour == $idProposition){
                            if($status == StatusProposition::gagnante){
                                $cellule = "<td class='propositionGagnante'>";
                                $propositionGagnante = $proposition->getTitreProposition();
                            }else if($status == StatusProposition::perdante){
                                $cellule = "<td class='propositionPerdante'>";
                            }

                            if(!$estPremiereColonne && $pourcentage - $pourcentagePrecedent > 0){
                                $cellule .= "<span>(+" . $pourcentage - $pourcentagePrecedent . "%)</span>";
                            }

                            $pourcentagePrecedent = $pourcentage;
                            $cellule .= $pourcentage .= "%";
                            $estPremiereColonne = false;
                            break;
                        }
                    }

                    if($cellule == "<td>"){
                        $cellule = "<td class='caseVide'>-</td>";
                    }else{
                        $cellule .= "</td>";
                    }
                    $ligne .= $cellule;
                }

                $ligne .= "</tr>";
                $votesParProposition .= $ligne;
            }



            $html = <<<HTML
                <table>
                    <tr>
                        $header
                    </tr>
                    $votesParProposition
                </table>

                <p>$propositionGagnante est la proposition gagnante.</p>
            HTML;

            return $html;
        }

        /**
         * @return array Tous les décomptes de vote
         * */
        private function calculerResultats(): array{
            $question = $this->getQuestion();
            $idQuestion = $question->getIdQuestion();
            $nbVotesTotal = (new VoteRepository)->selectNombreDeVotantsEffectifs($idQuestion);
            $nbVotesMajoriteeAbsolue = $nbVotesTotal/2 + 1;
            $votes = VoteUtilisateur::creerListeDeVotes($idQuestion);
            $candidatsRestants = (new PropositionRepository())->selectAllByQuestion($idQuestion);
            $majoriteeAbsolue = false;
            $decomptes = [];
            $decomptesEnPourcentage = [];
            $indexTour = 0; //compteur de tours

            while(!$majoriteeAbsolue){
                $decomptes[$indexTour] = [];
                $decomptesEnPourcentage[$indexTour] = [];

                foreach($candidatsRestants as $proposition){
                    $idProposition = $proposition->getIdProposition();
                    $decomptes[$indexTour][$idProposition] = 0;
                }

                foreach($votes as $vote){
                    $decomptes[$indexTour][$vote->getPropositionFavorite()]++;
                }

                $idPropositionFaible = 0;
                $nbVotesFaible = POSIX_RLIMIT_INFINITY;
                //recherche majoritaire, cas échéant indexTour++ et on dégage le plus faible
                foreach($decomptes[$indexTour] as $idProposition => $nbVotes){
                    $decomptesEnPourcentage[$indexTour][$idProposition][0] = round(($nbVotes * 100)/$nbVotesTotal);
                    $decomptesEnPourcentage[$indexTour][$idProposition][1] = StatusProposition::normale;

                    if($nbVotes >= $nbVotesMajoriteeAbsolue){
                        $majoriteeAbsolue = true;
                        $decomptesEnPourcentage[$indexTour][$idProposition][1] = StatusProposition::gagnante;
                    }

                    if($nbVotes < $nbVotesFaible){
                        $idPropositionFaible = $idProposition;
                        $nbVotesFaible = $nbVotes;
                    }
                }

                if(!$majoriteeAbsolue){
                    foreach($votes as $vote){
                        $vote->supprimerVote($idPropositionFaible);
                    }
                    for($i = 0; $i < count($candidatsRestants); $i++){
                        if($candidatsRestants[$i]->getIdProposition() == $idPropositionFaible){
                            array_splice($candidatsRestants, $i, 1);
                            break;
                        }
                    }
                    $decomptesEnPourcentage[$indexTour][$idPropositionFaible][1] = StatusProposition::perdante;
                }

                $indexTour++;
            }

            return $decomptesEnPourcentage;
        }

        public function traiterVote(): void{
            $username = ConnexionUtilisateur::getUsernameSiConnecte();
            $question = $this->getQuestion();
            $idQuestion = $question->getIdQuestion();
            $AP_URL = "frontController.php?controller=proposition&action=afficherPropositions&idQuestion=$idQuestion";

            $estVotant = (new VotantRepository)->existsForQuestion($idQuestion, $username);
            if (!$estVotant)
                VoteController::error($AP_URL, "Vous n'êtes pas votant pour cette question");

            $propositions = (new PropositionRepository)->selectAllByQuestion($idQuestion);
            $nbPropositions = count($propositions);
            $idPropositions = array_map(function (Proposition $p) {
                return $p->getIdProposition();
            }, $propositions);


            $votes = [];
            $voteNumbers = [];
            foreach ($idPropositions as $idProposition) {
                $proposition = null;
                foreach ($propositions as $p) {
                    if ($p->getIdProposition() == $idProposition) {
                        $proposition = $p;
                        break;
                    }
                }

                $proposition = Proposition::castIfNotNull($proposition, $AP_URL, "Erreur lors du vote 2");

                $vote = $_POST["choix" . $idProposition];
                if(!isset($vote)){
                    VoteController::error($AP_URL, "Vous devez donner un numéro à chacune des propositions.");
                    return;
                }
                if($vote < 1 || $vote > $nbPropositions)
                {
                    VoteController::error($AP_URL, "Les propositions doivent être classés avec des nombres allant de 1 à " . $nbPropositions . ".");
                    return;
                }

                $votes[] = new Vote($proposition, $username, $vote);
                if(in_array($vote, $voteNumbers)){
                    VoteController::error($AP_URL, "Deux propositions ne peuvent pas avoir un même numéro.");
                    return;
                }
                $voteNumbers[] = $vote;
            }



            $aDejaVote = (new VoteRepository)->existsForQuestion($idQuestion, $username);
            if ($aDejaVote) {
                (new VoteRepository)->deleteAllByQuestionEtVotant($idQuestion, $username);
                foreach ($votes as $vote) {
                    (new VoteRepository)->insert($vote);
                }
                $message = "Votre vote a bien été modifié";
            } else {
                foreach ($votes as $vote) {
                    (new VoteRepository)->insert($vote);
                }
                $message = "Votre vote a bien été pris en compte";
            }

            VoteController::message($AP_URL, $message);


        }
    }
}


namespace App\SAE\Model\SystemeVote\VoteAlternatif{

    use App\SAE\Model\DataObject\Vote;
    use App\SAE\Model\Repository\VoteRepository;

    class VoteUtilisateur{
        private array $listeVotes;
        private string $usernameVotant;

        /**
         * @return array La liste des votes des utilisateurs dans l'ordre de préférence
         * */
        public static function creerListeDeVotes(int $idQuestion): array{
            $votes = (new VoteRepository())->selectAllByQuestion($idQuestion);
            $votesOrdonnes = [];

            foreach($votes as $vote){
                $usernameVotant = $vote->getUsernameVotant();

                if(!isset($votesOrdonnes[$usernameVotant])){
                    $votesOrdonnes[$usernameVotant] = new VoteUtilisateur();
                }

                $votesOrdonnes[$usernameVotant]->ajouterVote($vote);
            }

            return $votesOrdonnes;
        }

        /**
         * Crée une PriorityQueue <b>vide</b> des votes dans l'ordre de préférence de l'utilisateur
         * */
        private function __construct(){
            $this->listeVotes = [];
        }

        /**
         * Ajoute un vote aux votes de l'utilisateur
         * */
        public function ajouterVote(Vote $vote){
            $nbVotes = count($this->listeVotes);
            for($i = 0; $i <= $nbVotes; $i++){
                if($i == $nbVotes){
                    $this->listeVotes[] = $vote;
                }else if($this->listeVotes[$i]->getValeur() > $vote->getValeur()){
                    $temp = $this->listeVotes[$i];
                    $this->listeVotes[$i] = $vote;
                    $vote = $temp;
                }
            }
        }

        /**
         * Supprime le vote d'id @param int $idProposition des votes de l'utilisateur
         * */
        public function supprimerVote(int $idProposition){
            for($i = 0; $i < count($this->listeVotes); $i++){
                if($this->listeVotes[$i]->getIdProposition() == $idProposition){
                    array_splice($this->listeVotes, $i, 1);
                    break;
                }
            }
        }

        /**
         * @return int L'id de la proposition favorite de l'utilisateur
         * */
        public function getPropositionFavorite(): ?int{
            return count($this->listeVotes) > 0 ? $this->listeVotes[0]->getIdProposition() : null;
        }
    }
}