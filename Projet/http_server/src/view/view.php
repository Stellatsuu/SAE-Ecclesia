<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title><?php echo $titrePage; ?></title>
    <link rel="stylesheet" href="scss/style.css">
</head>

<body>
<header>
    <input type="checkbox" id="mobileOpen"/>
    <div id="mobileMenu">
        <label for="mobileOpen">
            <img src="assets/images/logoSite.svg"/>
        </label>
        <div></div>
    </div>
    <nav>
        <ul>
            <li><a href="frontController.php">Accueil</a></li>
            <li><a href="frontController.php?controller=question&action=listerMesQuestions">Questions</a></li>
            <li><a href="frontController.php?controller=question&action=afficherQuestionsFinies">Résultats</a></li>
            <li><a href="frontController.php?controller=demandeQuestion&action=listerDemandesQuestion">Demandes</a></li>
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
</main>

<footer id="">
</footer>
</body>

</html>