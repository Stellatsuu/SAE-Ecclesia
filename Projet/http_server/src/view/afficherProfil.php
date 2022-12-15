<?php

use App\SAE\Model\DataObject\Utilisateur;

$utilisateur = Utilisateur::castIfNotNull($utilisateur);

$b64img = $utilisateur->getPhotoProfil();

$pfp = <<<html
    <img src="data:image/png;charset=utf8;base64,$b64img"/>
    html;
?>


<div class="panel" id="afficherProfil">

    <h1>Mon compte</h1>

    <div id="contenuProfil">
        <div id="PPusername">
            <a class="modal-open" href="#modalModifierPFP" id="PP">
                <?= $pfp ?>
            </a>
            <p><?php echo $utilisateur->getUsername(); ?></p>
            <img src='./assets/images/image-icon.svg' id="editPP" alt="editImageIcon"/>
        </div>

        <div id="informations">
            <label>Prénom et Nom</label>
            <a class="modal-open" href="#modalModifierNomPrenom"><img src='./assets/images/pen-ico.svg' class="pen" alt="editPenIcon"/></a>
            <p><?php echo $utilisateur->getPrenom() . " " . $utilisateur->getNom(); ?></p>


            <label>Adresse mail</label>
            <a class="modal-open" href="#modalModifierEmail"><img src='./assets/images/pen-ico.svg' class="pen" alt="editPenIcon"/></a>
            <p><?php echo $utilisateur->getEmail(); ?></p>

            <label>Mot de passe</label>
            <a class="modal-open" href="#modalModifierMDP"><img src='./assets/images/pen-ico.svg' class="pen" alt="editPenIcon"/></a>
        </div>
    </div>

    <a class="modal-open" href="#modalSupprimerCompte">Supprimer mon compte</a>

</div>

<div class="modal" id="modalModifierPFP">
    <div class="modalContent panel">
        <h2>Modifier ma photo de profil</h2>

        <form enctype="multipart/form-data" action="frontController.php?controller=utilisateur&action=modifierPFP" method="post" enctype="multipart/form-data">
            <input type="hidden" name="MAX_FILE_SIZE" value="8000000" />
            <input type="file" name="userfile" accept="image/png" required>
            <input type="submit" value="Modifier">
        </form>

        <a href="#" class="modal-close">
            <img src="assets/images/close-icon.svg" alt="bouton fermer">
        </a>
    </div>
</div>

<div class="modal" id="modalModifierNomPrenom">
    <div class="modalContent panel">
        <h2>Modifier mon nom/prénom</h2>

        <form action="frontController.php?controller=utilisateur&action=modifierNomPrenom" method="post">
            <label>Nom</label>
            <input type="text" name="nom" value="<?php echo $utilisateur->getNom(); ?>" required>

            <label>Prénom</label>
            <input type="text" name="prenom" value="<?php echo $utilisateur->getPrenom(); ?>" required>

            <input type="submit" value="Modifier">
        </form>

        <a href="#" class="modal-close">
            <img src="assets/images/close-icon.svg" alt="bouton fermer">
        </a>
    </div>
</div>

<div class="modal" id="modalModifierEmail">
    <div class="modalContent panel">
        <h2>Modifier mon adresse mail</h2>

        <form action="frontController.php?controller=utilisateur&action=modifierEmail" method="post">
            <label>Adresse mail</label>
            <input type="email" name="email" value="<?php echo $utilisateur->getEmail(); ?>" required>

            <input type="submit" value="Modifier">
        </form>

        <a href="#" class="modal-close">
            <img src="assets/images/close-icon.svg" alt="bouton fermer">
        </a>
    </div>
</div>

<div class="modal" id="modalModifierMDP">
    <div class="modalContent panel">
        <h2>Modifier mon mot de passe</h2>

        <form action="frontController.php?controller=utilisateur&action=modifierMDP" method="post">
            <label>Mot de passe actuel</label>
            <input type="password" name="ancienMDP" required>

            <label>Nouveau mot de passe</label>
            <input type="password" name="nouveauMDP" required>

            <label>Confirmer le nouveau mot de passe</label>
            <input type="password" name="nouveauMDP2" required>

            <input type="submit" value="Modifier">
        </form>

        <a href="#" class="modal-close">
            <img src="assets/images/close-icon.svg" alt="bouton fermer">
        </a>
    </div>
</div>

<div class="modal" id="modalSupprimerCompte">
    <div class="modalContent panel">
        <h2>Supprimer mon compte</h2>

        <p style="color: red;">Attention, cette action est irréversible !</p>

        <form action="frontController.php?controller=utilisateur&action=supprimerCompte" method="post" autocomplete="off">
            <input autocomplete="false" name="hidden" type="text" style="display:none;"> <!-- Pour éviter que le navigateur propose de remplir le formulaire -->

            <label>Entrez votre mot de passe</label>
            <input type="password" name="mdp_suppression" required>

            <input type="checkbox" name="confirmation" id="confirmation" required>
            <label>Je comprends que cette action est irréversible</label>

            <input type="submit" value="Supprimer mon compte">
        </form>

        <a href="#" class="modal-close">
            <img src="assets/images/close-icon.svg" alt="bouton fermer">
        </a>
    </div>
</div>