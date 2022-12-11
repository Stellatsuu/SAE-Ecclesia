<?php

use App\SAE\Lib\ConnexionUtilisateur;

$estConnecte = ConnexionUtilisateur::estConnecte();

if ($estConnecte) {
    $liensComptes = <<<html
    <li><a href="frontController.php?controller=utilisateur&action=afficherProfil">Mon Compte</a></li>
    <li><a href="frontController.php?controller=utilisateur&action=afficherParametres">Paramètres</a></li>
    <li><a href="frontController.php?controller=utilisateur&action=seDeconnecter">Se déconnecter</a></li>
html;
} else {
    $liensComptes = <<<html
    <li><a href="#modalSeConnecter">Se connecter</a></li>
    <li><a href="#modalCreerCompte">Créer un compte</a></li>
html;
}

$liensComptesVersionMobile = preg_replace("/<li>/", "<li class='onlyOnMobile'>", $liensComptes);
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
            <div>

            </div>
        </div>
        <nav>
            <ul>
                <li><a href="frontController.php">Accueil</a></li>
                <li><a href="frontController.php?controller=question&action=listerMesQuestions">Questions</a></li>
                <li><a href="frontController.php?controller=question&action=afficherQuestionsFinies">Résultats</a></li>
                <li><a href="frontController.php?controller=demandeQuestion&action=listerDemandesQuestion">Demandes</a></li>
                <?= $liensComptesVersionMobile ?>
            </ul>
            <div class="menu_compte" tabindex="0">
                <a class="bouton_ouvrir_compte" href="#"><img src="assets/images/pfp_ecclesia.jpg" alt="Compte"></a>
                <ul>
                    <?= $liensComptes ?>
                </ul>
            </div>
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

            <div id="seConnecterPanel" class="modal-content panel">
                <form action="frontController.php?controller=utilisateur&action=seConnecter" method="POST">
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
                        <input type="submit" value="Connexion">
                    </div>

                    <p>Pas inscrit ? <a href="#modalCreerCompte">Créer un compte</a></p>

                    <a href="#" class="modal-close">
                        <img src="assets/images/close-icon.svg" alt="bouton fermer">
                    </a>
                </form>
            </div>

        </div>
        <div id="modalCreerCompte" class="modal">
            <form action="">
                <div id="creerComptePanel" class="modal-content panel">
                    <h2>Créer un compte</h2>
                    <!-- TODO: Ajouter le formulaire de création de compte -->

                    <a href="#" class="modal-close">
                        <img src="assets/images/close-icon.svg" alt="bouton fermer">
                    </a>
                </div>
            </form>
        </div>
    </main>

    <footer id="">
    </footer>
</body>

</html>