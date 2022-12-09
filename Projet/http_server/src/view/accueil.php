<?php

use App\SAE\Model\DataObject\Utilisateur;
use App\SAE\Model\HTTP\Session;
use App\SAE\Model\Repository\UtilisateurRepository;

    $session = Session::getInstance();
    if($session->contient("idUtilisateur")) {
        $idUtilisateur = $session->lire("idUtilisateur");
        $message = "connecté en tant que " . $idUtilisateur;
    } else {
        $message = "non connecté";
    }


?>

<div class="panel">
    <h1>Accueil</h1>
    <h2>Bienvenue sur le site de E:cclesia</h2>
    <p>Vous êtes <?php echo $message; ?></p>

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


    <a class="button validerBtn" href="frontController.php?controller=main&action=seConnecter&idUtilisateur=10000">Se connecter en tant que 10000 (P. Valicov)</a>
    <a class="button validerBtn" href="frontController.php?controller=main&action=seConnecter&idUtilisateur=10001">Se connecter en tant que 10001 (A. Chollet)</a>
    <a class="button validerBtn" href="frontController.php?controller=main&action=seConnecter&idUtilisateur=10002">Se connecter en tant que 10002 (X. Palleja)</a>
    <a class="button validerBtn" href="frontController.php?controller=main&action=seConnecter&idUtilisateur=10003">Se connecter en tant que 10003 (N. Palleja)</a>
    <a class="button validerBtn" href="frontController.php?controller=main&action=seConnecter&idUtilisateur=10004">Se connecter en tant que 10004 (A. Marie-Jeanne)</a>
    <a class="button validerBtn" href="frontController.php?controller=main&action=seConnecter&idUtilisateur=10010">Se connecter en tant que 10010 (Troll)</a>

    <a class="button refuserBtn" href="frontController.php?controller=main&action=resetDatabase">Réinitialiser la base de données</a>

</div>