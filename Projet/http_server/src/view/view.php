<?php

use App\SAE\Lib\ConnexionUtilisateur;
$compteHref = ConnexionUtilisateur::estConnecte() ? "frontController.php?controller=utilisateur&action=afficherProfil" : "#modalSeConnecter";

$seDeconnecterLi = ConnexionUtilisateur::estConnecte() ? "<li><a href='frontController.php?controller=utilisateur&action=seDeconnecter'>
<img src='assets/images/deconnexion.png' alt='Se déconnecter' style='width: 50px; height: 50px;'>
</a></li>" : "";
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title><?php echo $titrePage; ?></title>
    <link rel="stylesheet" href="scss/style.css">
</head>

<body>
    <header>
        <input type="checkbox" id="mobileOpen" />
        <div id="mobileMenu">
            <label for="mobileOpen">
                <img src="assets/images/logoSite.svg" />
            </label>
            <div></div>
        </div>
        <nav>
            <ul>
                <li><a href="frontController.php">Accueil</a></li>
                <li><a href="frontController.php?controller=question&action=listerMesQuestions">Questions</a></li>
                <li><a href="frontController.php?controller=question&action=afficherQuestionsFinies">Résultats</a></li>
                <li><a href="frontController.php?controller=demandeQuestion&action=listerDemandesQuestion">Demandes</a></li>
                <li><a href="<?= $compteHref ?>">Compte</a></li>
                <?= $seDeconnecterLi ?>
            </ul>
            <div></div>
        </nav>
    </header>

    <main>
        <?php

        use App\SAE\Lib\MessageFlash;

        if (MessageFlash::contientMessage("info")) {
            $messages = MessageFlash::lireMessages("info");
            foreach ($messages as $message) {
                echo "<div class='message'>" . $message["message"] . "</div>";
            }
        }

        if (MessageFlash::contientMessage("error")) {
            $messages = MessageFlash::lireMessages("error");
            foreach ($messages as $message) {
                echo "<div class='errorMessage'>" . $message["message"] . "</div>";
            }
        }

        require __DIR__ . "/{$contenuPage}";
        ?>

        <div id="modalSeConnecter" class="modal">
            <form action="frontController.php?controller=utilisateur&action=seConnecter" method="POST">
                <div class="modal-content panel">
                    <h2>Se connecter</h2>
                    <div>
                        <label for="username">Nom d'utilisateur</label>
                        <input type="text" name="username" required>
                    </div>
                    <div>
                        <label for="password">Mot de passe</label>
                        <input type="password" name="password" required>
                    </div>
                    <div>
                        <input type="hidden" value="<?= $_SERVER['REQUEST_URI'] ?>" name="redirect">
                    </div>

                    <div>
                        <input type="submit" value="Se connecter">
                    </div>

                    <a href="#" class="modal-close">
                        <img src="assets/images/close-icon.svg" alt="bouton fermer">
                    </a>
                </div>
            </form>
        </div>
        <div id="modalCreerCompte" class="modal">
            <!-- TODO -->
        </div>
    </main>

    <footer id="">
    </footer>
</body>

</html>