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
                <li><a href="">Voter</a></li>
                <li><a href="frontController.php?action=listerMesQuestions&idUtilisateur=10005">Mes questions</a></li>
                <li><a href="">Mes groupes</a></li>
                <li><a href="">Mon compte</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php
        require __DIR__ . "/{$contenuPage}";
        ?>
    </main>

    <footer id="">
    </footer>
</body>

</html>