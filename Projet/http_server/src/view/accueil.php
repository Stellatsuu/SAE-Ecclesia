<?php

use App\SAE\Model\HTTP\Session;

    $session = Session::getInstance();
    if($session->contient("username")) {
        $username = htmlspecialchars($session->lire("username"));
        $message = "Vous êtes actuellement connecté en tant que " . $username;
    } else {
        $message = "Vous n'êtes pas connecté";
    }


?>

<div class="panel">
    <h1>Accueil</h1>
    <h2>Bienvenue sur le site de E:cclesia</h2>
    <p><?php echo $message ?></p>

    <a class="button" href="frontController.php?controller=question&action=listerMesQuestions">Mes questions (id session)</a>
    <p>-----------------------</p>
    <a class="button" href="frontController.php?controller=proposition&action=afficherFormulaireEcrireProposition&idQuestion=1">Ecrire nouvelle proposition (question id 1, "Blind Test")</a>
    <a class="button" href="frontController.php?controller=coAuteur&action=afficherFormulaireDemanderCoAuteur&idProposition=1">Demander à être co-auteur pour la proposition id 1</a>
    <a class="button" href="frontController.php?controller=coAuteur&action=afficherFormulaireGererCoAuteurs&idProposition=1">Gerer co-auteurs (proposition id 1)</a>
    <p>-----------------------</p>
    <a class="button" href="frontController.php?controller=proposition&action=afficherFormulaireContribuerProposition&idProposition=1">Contribuer à la proposition en tant que co-auteur (id 1)</a>
    <a class="button" href="frontController.php?controller=proposition&action=afficherPropositions&idQuestion=1">Afficher les propositions (question id 1)</a>
    <a class="button" href="frontController.php?controller=question&action=afficherResultats&idQuestion=1">Afficher les résultats (question id 1)</a>

    <a class="button refuserBtn" href="frontController.php?controller=debug&action=resetDatabase">Réinitialiser la base de données</a>
    <a class="button refuserBtn" href="frontController.php?controller=debug&action=resetDatabase&randomFakeUsers=50&randomFakeQuestions=100">Réinitialiser la base de données<br> + génération aléatoire</a>

</div>
