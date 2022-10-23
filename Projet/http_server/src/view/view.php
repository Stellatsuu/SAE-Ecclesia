<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php echo $titrePage; ?></title>
    <link rel="stylesheet" href="../../../web/css/style.css">
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="frontController.php?action=listerDemandesQuestion">Liste des questions</a></li>
                <li><a href="frontController.php?action=afficherFormulaireDemandeQuestion">Poser une question</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php
        require __DIR__ . "/{$contenuPage}";
        ?>
    </main>

    <footer id="complex-gradient-transition">
        <p>Le js c'est cool</p>
    </footer>

    <script src="../../web/js/anim.js"></script>    
</body>

</html>