<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php echo $titre; ?></title>
    <link rel="stylesheet" href="../../../web/css/style.css">
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="frontController.php?action=listerQuestions">Liste des questions</a></li>
                <li><a href="frontController.php?action=afficherDemandeQuestion">Poser une question</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php
        require __DIR__ . "/{$contenu}";
        ?>
    </main>

    <footer>
        <p>Le css c'est cool</p>
    </footer>
</body>

</html>