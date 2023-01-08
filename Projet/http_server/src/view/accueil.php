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

    <a class="button refuserBtn" href="frontController.php?controller=debug&action=resetDatabase">Réinitialiser la base de données</a>
    <a class="button refuserBtn" href="frontController.php?controller=debug&action=resetDatabase&randomFakeUsers=50&randomFakeQuestions=100">Réinitialiser la base de données<br> + génération aléatoire</a>

</div>
