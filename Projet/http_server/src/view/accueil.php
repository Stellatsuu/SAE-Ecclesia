<?php

use App\SAE\Model\DataObject\Utilisateur;
use App\SAE\Model\HTTP\Session;
use App\SAE\Model\Repository\UtilisateurRepository;

    $session = Session::getInstance();
    if($session->contient("username")) {
        $username = $session->lire("username");
        $message = "Vous êtes actuellement connecté en tant que " . $username;
    } else {
        $message = "Vous n'êtes pas connecté";
    }


?>

<div class="panel">
    <h1>Accueil</h1>
    <h2>Bienvenue sur le site de E:cclesia</h2>
    <p><?php echo $message ?></p>

    <a class="button" href="frontController.php?controller=demandeQuestion&action=afficherFormulaireDemandeQuestion">Demande question</a>
    <a class="button" href="frontController.php?controller=demandeQuestion&action=listerDemandesQuestion">Lister les demandes de questions</a>
    <a class="button" href="frontController.php?controller=question&action=listerMesQuestions">Mes questions (id session)</a>
    <p>-----------------------</p>
    <a class="button" href="frontController.php?controller=proposition&action=afficherFormulaireEcrireProposition&idQuestion=1">Ecrire nouvelle proposition (question id 1, "Blind Test")</a>
    <a class="button" href="frontController.php?controller=coAuteur&action=afficherFormulaireDemanderCoAuteur&idProposition=1">Demander à être co-auteur pour la proposition id 1</a>
    <a class="button" href="frontController.php?controller=coAuteur&action=afficherFormulaireGererCoAuteurs&idProposition=1">Gerer co-auteurs (proposition id 1)</a>
    <p>-----------------------</p>
    <a class="button" href="frontController.php?controller=proposition&action=afficherFormulaireContribuerProposition&idProposition=1">Contribuer à la proposition en tant que co-auteur (id 1)</a>
    <a class="button" href="frontController.php?controller=proposition&action=afficherPropositions&idQuestion=1">Afficher les propositions (question id 1)</a>
    <a class="button" href="frontController.php?controller=question&action=afficherResultats&idQuestion=1">Afficher les résultats (question id 1)</a>
    <p>-----------------------</p>
    <a class="button" href="frontController.php?controller=question&action=afficherQuestionsFinies">Lister les questions finies</a>


    <a class="button validerBtn" href="frontController.php?controller=main&action=seConnecter&username=petruv">Se connecter en tant que petruv (P. Valicov)</a>
    <a class="button validerBtn" href="frontController.php?controller=main&action=seConnecter&username=chatoine">Se connecter en tant que chatoine (A. Chollet)</a>
    <a class="button validerBtn" href="frontController.php?controller=main&action=seConnecter&username=xavierp">Se connecter en tant que xavierp (X. Palleja)</a>
    <a class="button validerBtn" href="frontController.php?controller=main&action=seConnecter&username=nathaliep">Se connecter en tant que nathaliep (N. Palleja)</a>
    <a class="button validerBtn" href="frontController.php?controller=main&action=seConnecter&username=alainmj">Se connecter en tant que alainmj (A. Marie-Jeanne)</a>
    <a class="button validerBtn" href="frontController.php?controller=main&action=seConnecter&username=trollface">Se connecter en tant que trollface (Troll)</a>

    <a class="button refuserBtn" href="frontController.php?controller=main&action=resetDatabase">Réinitialiser la base de données</a>
    <a class="button refuserBtn" href="frontController.php?controller=main&action=resetDatabase&randomFakeUsers=20&randomFakeQuestions=100">Réinitialiser la base de données<br> + 20 utilisateurs et 100 questions random</a>

</div>