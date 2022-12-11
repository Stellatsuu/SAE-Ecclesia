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

    <a href="#modalModifierPFP">
        <?= $pfp ?>
    </a>

    <label>Username</label>
    <p><?php echo $utilisateur->getUsername(); ?></p>

    <label>Nom</label>
    <p><?php echo $utilisateur->getNom(); ?></p>
    <a href="#modalModifierNomPrenom">Modifier</a>

    <label>Prénom</label>
    <p><?php echo $utilisateur->getPrenom(); ?></p>
    <a href="#modalModifierNomPrenom">Modifier</a>

    <label>Adresse mail</label>
    <p><?php echo $utilisateur->getEmail(); ?></p>
    <a href="#modalModifierEmail">Modifier</a>

    <a href="#modalModifierMDP">Modifier mon mot de passe</a>

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