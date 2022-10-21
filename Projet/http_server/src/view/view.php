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
            <p>Imaginez un nav ici</p>
        </nav>
    </header>

    <main>
        <?php
        require __DIR__ . "/{$contenu}";
        ?>
    </main>

    <footer>
        <p>Imaginez un footer ici</p>
    </footer>
</body>

</html>