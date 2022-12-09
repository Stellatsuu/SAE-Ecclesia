<?php

use App\SAE\Lib\Markdown;
use PHPUnit\Framework\TestCase;
use App\SAE\Model\Repository\DatabaseConnection;

/* autoloader */
require_once __DIR__ . '/../src/Lib/Psr4AutoloaderClass.php';
$loader = new App\SAE\Lib\Psr4AutoloaderClass();
// register the base directories for the namespace prefix
$loader->addNamespace('App\SAE', __DIR__ . '/../src');
// register the autoloader
$loader->register();

class MarkdownTest extends TestCase
{

    public function testTexteEnGras(){
        $texte = "je suis un texte en **gras**";
        $attendu = "<p>je suis un texte en <b>gras</b></p>";

        self::assertEquals($attendu, Markdown::toHtml($texte));
    }

    public function testTextePasEnGras(){
        $texte = "je suis un texte pas en \*\*gras\*\*";
        $attendu = "<p>je suis un texte pas en **gras**</p>";

        self::assertEquals($attendu, Markdown::toHtml($texte));
    }

    public function testTexteSouligne(){
        $texte = "je suis un texte __souligné__";
        $attendu = "<p>je suis un texte <u>souligné</u></p>";

        self::assertEquals($attendu, Markdown::toHtml($texte));
    }

    public function testTextePasSouligne(){
        $texte = "je suis un texte pas \_\_souligné\_\_";
        $attendu = "<p>je suis un texte pas __souligné__</p>";

        self::assertEquals($attendu, Markdown::toHtml($texte));
    }

    public function testTexteItaliqueAvecEtoile(){
        $texte = "je suis un texte en *italique*";
        $attendu = "<p>je suis un texte en <i>italique</i></p>";

        self::assertEquals($attendu, Markdown::toHtml($texte));
    }

    public function testTexteItaliqueAvecUnderline(){
        $texte = "je suis un texte en _italique_";
        $attendu = "<p>je suis un texte en <i>italique</i></p>";

        self::assertEquals($attendu, Markdown::toHtml($texte));
    }

    public function testTexteSansItaliqueAvecUnderlineEtEtoile(){
        $texte = "je suis un texte pas en _italique*";
        $attendu = "<p>je suis un texte pas en _italique*</p>";

        self::assertEquals($attendu, Markdown::toHtml($texte));
    }

    public function testTexteSansItaliqueAvecEtoileEtUnderline(){
        $texte = "je suis un texte pas en *italique_";
        $attendu = "<p>je suis un texte pas en *italique_</p>";

        self::assertEquals($attendu, Markdown::toHtml($texte));
    }

    public function testTexteSansItaliqueAvecUnderlinee(){
        $texte = "je suis un texte pas en \*italique\*";
        $attendu = "<p>je suis un texte pas en *italique*</p>";

        self::assertEquals($attendu, Markdown::toHtml($texte));
    }

    public function testTexteSansItaliqueAvecEtoile(){
        $texte = "je suis un texte pas en \_italique\_";
        $attendu = "<p>je suis un texte pas en _italique_</p>";

        self::assertEquals($attendu, Markdown::toHtml($texte));
    }

    public function testTexteTitre1(){
        $texte = "#je suis un titre 1";
        $attendu = "<h1>je suis un titre 1</h1>";

        self::assertEquals($attendu, Markdown::toHtml($texte));
    }

    public function testTexteTitre2(){
        $texte = "##je suis un titre 2";
        $attendu = "<h2>je suis un titre 2</h2>";

        self::assertEquals($attendu, Markdown::toHtml($texte));
    }

    public function testTexteTitre3(){
        $texte = "###je suis un titre 3";
        $attendu = "<h3>je suis un titre 3</h3>";

        self::assertEquals($attendu, Markdown::toHtml($texte));
    }

    public function testTexteTitre4(){
        $texte = "####je suis un titre 4";
        $attendu = "<h4>je suis un titre 4</h4>";

        self::assertEquals($attendu, Markdown::toHtml($texte));
    }

    public function testTexteTitre5(){
        $texte = "#####je suis un titre 5";
        $attendu = "<h5>je suis un titre 5</h5>";

        self::assertEquals($attendu, Markdown::toHtml($texte));
    }

    public function testTexteTitre6(){
        $texte = "######je suis un titre 6";
        $attendu = "<h6>je suis un titre 6</h6>";

        self::assertEquals($attendu, Markdown::toHtml($texte));
    }

    public function testTexteTitre1NonFonctionnelAuMilieuDuneLigne(){
        $texte = "texte avant #je ne suis pas un titre 1";
        $attendu = "<p>texte avant #je ne suis pas un titre 1</p>";

        self::assertEquals($attendu, Markdown::toHtml($texte));
    }

    public function testListeTirets(){
        $texte = "- element1\n- element2\n- element3";
        $attendu = "<ul><li>element1</li><li>element2</li><li>element3</li></ul>";

        self::assertEquals($attendu, Markdown::toHtml($texte));
    }

    public function testListeEtoiles(){
        $texte = "* element1\n* element2\n* element3";
        $attendu = "<ul><li>element1</li><li>element2</li><li>element3</li></ul>";

        self::assertEquals($attendu, Markdown::toHtml($texte));
    }

    public function testListeEtoilesEtTirets(){
        $texte = "- element1\n* element2\n* element3";
        $attendu = "<ul><li>element1</li><li>element2</li><li>element3</li></ul>";

        self::assertEquals($attendu, Markdown::toHtml($texte));
    }

    public function testLien(){
        $texte = "ceci est un texte avec un [lien](https://vers.ici)";
        $attendu = "<p>ceci est un texte avec un <a href=\"https://vers.ici\" alt=\"lien\">lien</a></p>";

        self::assertEquals($attendu, Markdown::toHtml($texte));
    }

    public function testLienNeDebordePas(){
        $texte = "ceci est un texte avec un [ [lien](https://vers.ici) )";
        $attendu = "<p>ceci est un texte avec un [ <a href=\"https://vers.ici\" alt=\"lien\">lien</a> )</p>";

        self::assertEquals($attendu, Markdown::toHtml($texte));
    }

    public function testGlobal(){
        $texte = '#Il est essentiellement connu en France pour sa série du Guide du voyageur galactique, space opera loufoque et délirant proche de l’esprit des meilleurs Monty Python, qui a remporté un succès considérable dans les pays anglo-saxons. Adapté d’un feuilleton radiophonique diffusé sur la BBC au printemps 1978, Le Guide du voyageur galactique a également connu les honneurs d’une transposition télévisuelle kitschissime parfaitement inoubliable avant de devenir H2G2, le délire cinématographique de Garth Jennings et Nick Goldsmith.
Il est essentiellement connu en France pour sa série du Guide du voyageur galactique, space opera loufoque et délirant proche de l’esprit des meilleurs Monty Python, qui a remporté un succès considérable dans les pays anglo-saxons. Adapté d’un feuilleton radiophonique diffusé [sur] la [BBC](https://truc.bidule) au (printemps) 1978, Le Guide du voyageur galactique a également connu les honneurs d’une transposition télévisuelle kitschissime parfaitement inoubliable avant de devenir H2G2, le délire cinématographique de Garth Jennings et Nick Goldsmith.
Il s’agit tout __simplement__ de __***l’abréviation***__ du titre original *The **HitchHiker’s** Guide to the Galaxy*. Et c’est, depuis longtemps déjà, le cri de ralliement de tous les fans du *Guide du voyageur galactique* à travers le monde.
Il s’agit tout \__simplement_\_ de __*\*\*l’abréviation*\*\*__ du titre original *The **HitchHiker’s** Guide to the Galaxy*. Et c’est, depuis longtemps déjà, le cri de ralliement de tous les fans du *Guide du voyageur galactique* à travers le monde.
* Chocola\\\t
- *Kiwi*
* [Mayonnaise](#)
##Titre2
######Titre6
###Titre3
#####Titre5
####Titre4
#####';

        $attendu = '<h1>Il est essentiellement connu en France pour sa série du Guide du voyageur galactique, space opera loufoque et délirant proche de l’esprit des meilleurs Monty Python, qui a remporté un succès considérable dans les pays anglo-saxons. Adapté d’un feuilleton radiophonique diffusé sur la BBC au printemps 1978, Le Guide du voyageur galactique a également connu les honneurs d’une transposition télévisuelle kitschissime parfaitement inoubliable avant de devenir H2G2, le délire cinématographique de Garth Jennings et Nick Goldsmith.</h1><p>Il est essentiellement connu en France pour sa série du Guide du voyageur galactique, space opera loufoque et délirant proche de l’esprit des meilleurs Monty Python, qui a remporté un succès considérable dans les pays anglo-saxons. Adapté d’un feuilleton radiophonique diffusé [sur] la <a href="https://truc.bidule" alt="BBC">BBC</a> au (printemps) 1978, Le Guide du voyageur galactique a également connu les honneurs d’une transposition télévisuelle kitschissime parfaitement inoubliable avant de devenir H2G2, le délire cinématographique de Garth Jennings et Nick Goldsmith.<br/>Il s’agit tout <u>simplement</u> de <u><b><i>l’abréviation</i></b></u> du titre original <i>The <b>HitchHiker’s</b> Guide to the Galaxy</i>. Et c’est, depuis longtemps déjà, le cri de ralliement de tous les fans du <i>Guide du voyageur galactique</i> à travers le monde.<br/>Il s’agit tout _<i>simplement</i>_ de <u><i>**l’abréviation</i>**</u> du titre original <i>The <b>HitchHiker’s</b> Guide to the Galaxy</i>. Et c’est, depuis longtemps déjà, le cri de ralliement de tous les fans du <i>Guide du voyageur galactique</i> à travers le monde.</p><ul><li>Chocola\t</li><li><i>Kiwi</i></li><li><a href="#" alt="Mayonnaise">Mayonnaise</a></li></ul><h2>Titre2</h2><h6>Titre6</h6><h3>Titre3</h3><h5>Titre5</h5><h4>Titre4</h4><h5></h5>';

        self::assertEquals($attendu, Markdown::toHtml($texte));
    }
}
