<?php

use App\SAE\Lib\ConnexionUtilisateur;
use App\SAE\Lib\PhotoProfil;
use App\SAE\Model\DataObject\Utilisateur;
use App\SAE\Model\Repository\UtilisateurRepository;

$estConnecte = ConnexionUtilisateur::estConnecte();
$estAdmin = ConnexionUtilisateur::estAdmin();

$lienDemandes = $estAdmin ? <<<HTML
<li><a href="frontController.php?controller=demandeQuestion&action=listerDemandesQuestion">Demandes</a></li>
HTML : "";

if ($estConnecte) {
    $liensComptes = <<<html
    <li><a href="frontController.php?controller=utilisateur&action=afficherProfil">Mon compte</a></li>
    <li><a href="frontController.php?controller=question&action=listerMesQuestions">Mes questions</a></li>
    $lienDemandes
    <li><a href="frontController.php?controller=utilisateur&action=seDeconnecter">Se déconnecter</a></li>
    html;

    $utilisateur = (new UtilisateurRepository)->select(ConnexionUtilisateur::getUsername());

    if (!$utilisateur) {
        ConnexionUtilisateur::deconnecter();
        header("Location: " . "frontController.php");
        exit;
    }

    $utilisateur = Utilisateur::castIfNotNull($utilisateur);

    $b64img = $utilisateur->getPhotoProfil(64);

    $pfp = PhotoProfil::getBaliseImg($b64img, "photo de profil");
} else {
    $liensComptes = <<<html
    <li><a class="modal-open" href="#modalSeConnecter">Se connecter</a></li>
    <li><a class="modal-open" href="#modalCreerCompte">Créer un compte</a></li>
    html;

    $pfp = <<<html
    <img src="assets/images/defaultPFPs/disconnected.jpg"/>
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
                <li><a href="frontController.php?controller=question&action=listerQuestions">Questions</a></li>
                <li><a href="frontController.php?controller=question&action=afficherQuestionsFinies">Résultats</a></li>
                <li><a href="frontController.php?controller=demandeQuestion&action=afficherFormulaireDemandeQuestion">Poser une question</a></li>
                <?= $liensComptesVersionMobile ?>
            </ul>
            <div class="menu_compte" tabindex="0">
                <a class="bouton_ouvrir_compte" href="#"><?= $pfp ?></a>
                <ul>
                    <?= $liensComptes ?>
                </ul>
            </div>
        </nav>

    </header>

    <main>
        <div id="messagesFlash">
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
            ?>
        </div>

        <?php require __DIR__ . "/{$contenuPage}"; ?>

        <div id="modalSeConnecter" class="modal">

            <div id="seConnecterPanel" class="modal-content panel comptePanel">
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

                    <p>Pas inscrit ? <a class="modal-open" href="#modalCreerCompte">Créer un compte</a></p>

                    <a href="#" class="modal-close">
                        <img src="assets/images/close-icon.svg" alt="bouton fermer">
                    </a>
                </form>
            </div>

        </div>
        <div id="modalCreerCompte" class="modal">
            <div id="creerComptePanel" class="modal-content panel comptePanel">
                <form action="frontController.php?controller=utilisateur&action=sInscrire" method="POST">
                    <h2>S'inscrire</h2>


                    <div>
                        <label for="username">Nom d'utilisateur*</label>
                        <input type="text" name="username" required>
                    </div>
                    <div>
                        <label for="password" style="display: flex">Mot de passe*
                            <span class="tooltip">
                                <img class="tooltipImage" src="assets/images/info-icon.svg" alt="bouton info">
                                <div class="tooltiptext">Votre mot de passe doit contenir:
                                    <ul>
                                        <li>Au moins 8 caractères</li>
                                        <li>Au moins une lettre minuscule</li>
                                        <li>Au moins une lettre majuscule</li>
                                        <li>Au moins un chiffre</li>
                                        <li>Au moins un caractère spécial</li>
                                    </ul>
                                </div>
                            </span>
                        </label>

                        <input type="password" name="password" required>
                    </div>
                    <div>
                        <label for="passwordConfirmation">Confirmer le mot de passe*</label>
                        <input type="password" name="passwordConfirmation" required>
                    </div>

                    <div>
                        <label for="nom">Nom</label>
                        <input type="text" name="nom">
                    </div>

                    <div>
                        <label for="prenom">Prénom</label>
                        <input type="text" name="prenom">
                    </div>

                    <div>
                        <label for="email">Email</label>
                        <input type="email" name="email">
                    </div>

                    <div>
                        <input type="hidden" value="<?= $_SERVER['REQUEST_URI'] ?>" name="redirect">
                    </div>

                    <div>
                        <input type="submit" value="S'inscrire">
                    </div>

                    <p>Déjà inscrit ? <a class="modal-open" href="#modalSeConnecter">Se connecter</a></p>



                    <a href="#" class="modal-close">
                        <img src="assets/images/close-icon.svg" alt="bouton fermer">
                    </a>
                </form>
            </div>
        </div>
    </main>

    <footer id="">
    </footer>

    <script>
        const body = document.querySelector("body");
        const modalCloseList = document.querySelectorAll(".modal-close");
        const modalOpenList = document.querySelectorAll(".modal-open");

        //if url contains #modal, but not just #, add class no-scroll to body
        if (window.location.hash && window.location.hash !== "#") {
            body.classList.add("no-scroll");
        }


        modalOpenList.forEach((modalOpen) => {
            modalOpen.addEventListener("click", (e) => {
                body.classList.add("no-scroll");
            });
        });

        modalCloseList.forEach((modalClose) => {
            modalClose.addEventListener("click", (e) => {
                body.classList.remove("no-scroll");
            });
        });
    </script>
</body>

</html>