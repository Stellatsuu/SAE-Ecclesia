<?php

use App\SAE\Lib\ConnexionUtilisateur;
use App\SAE\Lib\PhotoProfil;
use App\SAE\Model\DataObject\Utilisateur;
use App\SAE\Model\Repository\UtilisateurRepository;

$estConnecte = ConnexionUtilisateur::estConnecte();
$estAdmin = ConnexionUtilisateur::estAdmin();
$username = ConnexionUtilisateur::getUsername();
$lienDemandes = $estAdmin ? <<<HTML
<li><a href="frontController.php?controller=demandeQuestion&action=listerDemandesQuestion">Demandes</a></li>
HTML : "";

if ($estConnecte) {
    $liensComptes = <<<html
    <li><a href="frontController.php?controller=utilisateur&action=afficherProfil">Mon compte</a></li>
    <li><a href="frontController.php?controller=question&action=listerQuestions&f_mq=true">Mes questions</a></li>
    $lienDemandes
    <li><a href="frontController.php?controller=utilisateur&action=seDeconnecter">Se déconnecter</a></li>
    html;

    $utilisateur = (new UtilisateurRepository)->select($username);

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
    <img src="assets/images/defaultPFPs/disconnected.jpg" alt="disconnectedPFP"/>
    html;
}

$liensComptesVersionMobile = preg_replace("/<li>/", "<li class='onlyOnMobile'>", $liensComptes);

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($titrePage) ?></title>

    <link rel="stylesheet" href="scss/style.css">
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon"/>
</head>

<body>
    <header>
        <input type="checkbox" id="mobileOpen" />
        <div id="mobileMenu">
            <label id="barreMenu" for="mobileOpen">
                <img src="assets/images/logoSite.svg" alt="websiteLogo" />
            </label>
            <?php if ($estConnecte) echo
             "<div id='pfpMobile'>
                 <label for='mobileOpen'>$username</label>
                 <label for='mobileOpen'>$pfp</label>
             </div>"?>
            <div id='fleches'></div>
        </div>
        <nav>
            <ul>
                <li><a href="frontController.php">Accueil</a></li>
                <li><a href="frontController.php?controller=question&action=listerQuestions">Questions</a></li>
                <li><a href="frontController.php?controller=question&action=listerQuestions&f_resultat=true">Résultats</a></li>
                <li><a href="frontController.php?controller=demandeQuestion&action=afficherFormulaireDemandeQuestion">Poser une question</a></li>
                <?= $liensComptesVersionMobile ?>
            </ul>
        </nav>
        <div class="menu_compte" tabindex="0">
            <a class="bouton_ouvrir_compte" href="#"><?= $pfp ?></a>
            <?php if($estConnecte) echo  "<label>$username</label>";?>
            <ul>
                <?= $liensComptes ?>
            </ul>
        </div>
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
                        <label for="username_c_id">Nom d'utilisateur</label>
                        <input id="username_c_id" type="text" name="username" required>
                    </div>
                    <div>
                        <label for="password_c_id">Mot de passe</label>
                        <input id="password_c_id" type="password" name="password" required>
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
                        <label for="username_i_id">Nom d'utilisateur*</label>
                        <input id="username_i_id" type="text" name="username" required>
                    </div>
                    <div>
                        <div class="password-label">
                            <label for="password_i_id">Mot de passe*</label>
                            <div class="tooltip">
                                <img class="tooltipImage" src="assets/images/info-icon.svg" alt="bouton info">
                                <div class="tooltiptext"> Votre mot de passe doit contenir:
                                    <ul>
                                        <li>Au moins 8 caractères</li>
                                        <li>Au moins une lettre minuscule</li>
                                        <li>Au moins une lettre majuscule</li>
                                        <li>Au moins un chiffre</li>
                                        <li>Au moins un caractère spécial</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <input id="password_i_id" type="password" name="password" required>
                    </div>
                    <div>
                        <label for="passwordConfirmation_id">Confirmer le mot de passe*</label>
                        <input id="passwordConfirmation_id" type="password" name="passwordConfirmation" required>
                    </div>

                    <div>
                        <label for="nom_id">Nom</label>
                        <input id="nom_id" type="text" name="nom">
                    </div>

                    <div>
                        <label for="prenom_id">Prénom</label>
                        <input id="prenom_id" type="text" name="prenom">
                    </div>

                    <div>
                        <label for="email_id">Email</label>
                        <input id="email_id" type="email" name="email">
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
