<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php echo $titrePage; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="">Accueil</a></li>
                <li><a href="">Voter</a></li>
                <li><a href="">Mes questions</a></li>
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