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
        <nav>
            <ul>
                <li><a href="frontController.php">Accueil</a></li>
                <li><a href="frontController.php?controller=demandeQuestion&action=afficherFormulaireDemandeQuestion">Demande question</a></li>
                <li><a href="frontController.php?controller=question&action=listerMesQuestions&idUtilisateur=10001">Mes questions</a></li>
                <li><a href="">Mes groupes</a></li>
                <li><a href="">Mon compte</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php
        if ($message) {
            require('message.php');
        }
        ?>
        <?php
        require __DIR__ . "/{$contenuPage}";
        ?>
    </main>

    <footer id="">
    </footer>
</body>

</html>