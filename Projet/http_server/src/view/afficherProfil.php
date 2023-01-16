<?php

use App\SAE\Lib\PhotoProfil;

//$utilisateur passé par le Controller
$username = htmlspecialchars($utilisateur->getUsername());
$prenom = htmlspecialchars($utilisateur->getPrenom());
$nom = htmlspecialchars($utilisateur->getNom());
$email = htmlspecialchars($utilisateur->getEmail());
//$pfpb64 passée par le Controller
$pfp = PhotoProfil::getBaliseImg($pfpb64, "photo de profil");
?>


<div class="panel" id="afficherProfil">

    <h1>Mon compte</h1>

    <div id="contenuProfil">
        <div id="PPusername">
            <p id="PP"><?= $pfp ?></p>
            <p><?= $username ?></p>
            <a class="modal-open" href="#modalModifierPFP"><img src='./assets/images/image-icon.svg' id="editPP" alt="editImageIcon"/></a>
        </div>

        <div id="informations">
            <label>Prénom et Nom</label>
            <a class="modal-open" href="#modalModifierNomPrenom"><img src='./assets/images/pen-ico.svg' class="pen" alt="editPenIcon"/></a>
            <p><?= $prenom . " " . $nom ?></p>


            <label>Adresse mail</label>
            <a class="modal-open" href="#modalModifierEmail"><img src='./assets/images/pen-ico.svg' class="pen" alt="editPenIcon"/></a>
            <p><?= $email ?></p>

            <label>Mot de passe</label>
            <a class="modal-open" href="#modalModifierMDP"><img src='./assets/images/pen-ico.svg' class="pen" alt="editPenIcon"/></a>
        </div>

    </div>
    <p class="button supprimerBtn"><a href="#modalSupprimerCompte">Supprimer mon compte</a></p>

</div>

<div class="modal" id="modalModifierPFP">
    <div class="modal-content panel modal-fit">
        <form enctype="multipart/form-data" action="frontController.php?controller=utilisateur&action=modifierPFP" method="post" enctype="multipart/form-data">
            <h2>Modifier ma photo de profil</h2>

            <input type="hidden" name="MAX_FILE_SIZE" value="8000000" />
            <input type="file" name="userfile" accept="image/png" required>
            <input type="submit" value="Modifier">

            <a href="#" class="modal-close">
                <img src="assets/images/close-icon.svg" alt="bouton fermer">
            </a>
        </form>
    </div>
</div>

<div class="modal" id="modalModifierNomPrenom">
    <div class="modal-content panel modal-fit">
        <form action="frontController.php?controller=utilisateur&action=modifierNomPrenom" method="post">
            <h2>Modifier mon nom/prénom</h2>

            <label>Nom</label>
            <input type="text" name="nom" value="<?= $nom ?>" required>

            <label>Prénom</label>
            <input type="text" name="prenom" value="<?= $prenom ?>" required>

            <input type="submit" value="Modifier">

            <a href="#" class="modal-close">
                <img src="assets/images/close-icon.svg" alt="bouton fermer">
            </a>
        </form>
    </div>
</div>

<div class="modal" id="modalModifierEmail">
    <div class="modal-content panel modal-fit">
        <form action="frontController.php?controller=utilisateur&action=modifierEmail" method="post">
            <h2>Modifier mon adresse mail</h2>

            <label>Adresse mail</label>
            <input type="email" name="email" value="<?= $email ?>" required>

            <input type="submit" value="Modifier">

            <a href="#" class="modal-close">
                <img src="assets/images/close-icon.svg" alt="bouton fermer">
            </a>
        </form>
    </div>
</div>

<div class="modal" id="modalModifierMDP">
    <div class="modal-content panel modal-fit">
        <form action="frontController.php?controller=utilisateur&action=modifierMDP" method="post">
            <h2>Modifier mon mot de passe</h2>

            <label>Mot de passe actuel</label>
            <input type="password" name="ancienMDP" required>

            <label>Nouveau mot de passe</label>
            <input type="password" name="nouveauMDP" required>

            <label>Confirmer le nouveau mot de passe</label>
            <input type="password" name="nouveauMDP2" required>

            <input type="submit" value="Modifier">

            <a href="#" class="modal-close">
                <img src="assets/images/close-icon.svg" alt="bouton fermer">
            </a>
        </form>

    </div>
</div>

<div class="modal" id="modalSupprimerCompte">
    <div class="modal-content panel modal-fit">
        <form action="frontController.php?controller=utilisateur&action=supprimerCompte" method="post" autocomplete="off">
            <h2>Supprimer mon compte</h2>

            <p style="color: red;">Attention, cette action est irréversible !</p>
            <!-- Pour éviter que le navigateur propose de remplir le formulaire. Parce que autocomplete="off" ne suffit plus, apparemment -->
            <input autocomplete="false" name="hidden" type="text" style="display:none;"> 

            <label>Entrez votre mot de passe</label>
            <input type="password" name="mdp_suppression" required>
            <div>
                <a class="button refuserBtn" href="#">Annuler</a>
                <input class="button validerBtn" type="submit" value="Confirmer">
            </div>
            <a href="#" class="modal-close">
                <img src="assets/images/close-icon.svg" alt="bouton fermer">
            </a>
        </form>
    </div>
</div>
