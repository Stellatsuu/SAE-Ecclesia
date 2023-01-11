<?php

namespace App\SAE\Lib;

class Markdown
{

    /**
     * @param string $chaineMarkdown La chaine de caractères en markdown à transformer en html
     * @return string La chaine de caractères en html
     */
    public static function toHtml(string $chaineMarkdown):string
    {
        $chaineMarkdown = htmlspecialchars($chaineMarkdown);
        $chaineMarkdown = self::toBulletHtml($chaineMarkdown);
        $chaineMarkdown = self::toTitle6Html($chaineMarkdown);
        $chaineMarkdown = self::toTitle5Html($chaineMarkdown);
        $chaineMarkdown = self::toTitle4Html($chaineMarkdown);
        $chaineMarkdown = self::toTitle3Html($chaineMarkdown);
        $chaineMarkdown = self::toTitle2Html($chaineMarkdown);
        $chaineMarkdown = self::toTitle1Html($chaineMarkdown);
        $chaineMarkdown = self::toLineBreakHtml($chaineMarkdown);
        $chaineMarkdown = self::toBoldHtml($chaineMarkdown);
        $chaineMarkdown = self::toUnderlineHtml($chaineMarkdown);
        $chaineMarkdown = self::toItalicHtml($chaineMarkdown);
        $chaineMarkdown = self::toLinkHtml($chaineMarkdown);
        $chaineMarkdown = self::toCleanHtml($chaineMarkdown);

        return $chaineMarkdown;
    }

    /**
     * @param string $chaineMarkdown La chaine de caractère en markdown
     * @return string La chaine de caractère avec le texte en <b>gras</b>
     */
    private static function toBoldHtml(string $chaineMarkdown): string
    {
        return preg_replace("/(?<!\\\\)\*\*([\w\W]*?[^\\\\][*]*)\*\*/m", "<b>$1</b>", $chaineMarkdown);
    }

    /**
     * @param string $chaineMarkdown La chaine de caractère en markdown
     * @return string La chaine de caractère avec le texte en <u>souligné</u>
     */
    private static function toUnderlineHtml(string $chaineMarkdown): string
    {
        return preg_replace("/(?<!\\\\)__([\w\W]*?[^\\\\])__/m", "<u>$1</u>", $chaineMarkdown);
    }

    /**
     * @param string $chaineMarkdown La chaine de caractère en markdown
     * @return string La chaine de caractère avec les retours à la ligne
     * */
    private static function toLineBreakHtml(string $chaineMarkdown): string
    {
        $chaineMarkdown = preg_replace("/(^|<\/(?:h[1-6]|ul)>)\n?([^<]{2,}?)\n?(<(?:h[1-6]|ul)>|$)/", "$1<p>$2</p>$3", $chaineMarkdown); // crée des paragraphes

        $chaineRetournee = "";
        while($chaineRetournee != $chaineMarkdown) {
            $chaineMarkdown = $chaineRetournee == "" ? $chaineMarkdown : $chaineRetournee;
            $chaineRetournee =  preg_replace("/(?<=<h[1-6]>|<p>|\n|<br\/>)([^<]*?)\n([^<]*?)(?=\n|<\/h[1-6]>|<\/p>)/", "$1<br/>$2", $chaineMarkdown); //place des retours à la ligne dans des paragraphes ou titres
        }

        return preg_replace("/\n/", "", $chaineRetournee); //place des retours à la ligne dans des paragraphes ou titres
    }

    /**
     * @param string $chaineMarkdown La chaine de caractère en markdown
     * @param int $tailleTitre La taille du titre
     * @return string La chaine de caractère avec les titres de taille $titleSize
     * */
    private static function toTitleHtml(string $chaineMarkdown, int $tailleTitre): string
    {
        return preg_replace("/^#{{$tailleTitre}}([^\n]*)/m", "<h$tailleTitre>$1</h$tailleTitre>", $chaineMarkdown);
    }

    /**
     * @param string $chaineMarkdown La chaine de caractère en markdown
     * @return string La chaine de caractère avec les <h1>titres de taille 1</h1>
     * */
    private static function toTitle1Html(string $chaineMarkdown): string
    {
        return self::toTitleHtml($chaineMarkdown, 1);
    }

    /**
     * @param string $chaineMarkdown La chaine de caractère en markdown
     * @return string La chaine de caractère avec les <h2>titres de taille 2</h2>
     * */
    private static function toTitle2Html(string $chaineMarkdown): string
    {
        return self::toTitleHtml($chaineMarkdown, 2);
    }

    /**
     * @param string $chaineMarkdown La chaine de caractère en markdown
     * @return string La chaine de caractère avec les <h3>titres de taille 3</h3>
     * */
    private static function toTitle3Html(string $chaineMarkdown): string
    {
        return self::toTitleHtml($chaineMarkdown, 3);
    }

    /**
     * @param string $chaineMarkdown La chaine de caractère en markdown
     * @return string La chaine de caractère avec les <h4>titres de taille 4</h4>
     * */
    private static function toTitle4Html(string $chaineMarkdown): string
    {
        return self::toTitleHtml($chaineMarkdown, 4);
    }

    /**
     * @param string $chaineMarkdown La chaine de caractère en markdown
     * @return string La chaine de caractère avec les <h5>titres de taille 5</h5>
     * */
    private static function toTitle5Html(string $chaineMarkdown): string
    {
        return self::toTitleHtml($chaineMarkdown, 5);
    }

    /**
     * @param string $chaineMarkdown La chaine de caractère en markdown
     * @return string La chaine de caractère avec les <h6>titres de taille 6</h6>
     * */
    private static function toTitle6Html(string $chaineMarkdown): string
    {
        return self::toTitleHtml($chaineMarkdown, 6);
    }

    /**
     * @param string $chaineMarkdown La chaine de caractère en markdown
     * @return string La chaine de caractère avec les <a href="">liens</a>
     * */
    private static function toLinkHtml(string $chaineMarkdown): string
    {
        return preg_replace("/(?<!\\\\)\[([^\[\]]*)]\(([^()]*)\)/m", "<a href=\"$2\" alt=\"$1\">$1</a>", $chaineMarkdown);
    }

    /**
     * @param string $chaineMarkdown La chaine de caractère en markdown
     * @return string La chaine de caractère avec les textes en <i>italique</i>
     * */
    private static function toItalicHtml(string $chaineMarkdown): string
    {
        $chaineMarkdown = preg_replace("/(?<!\\\\)\*([\w\W]*?[^\\\\])\*/m", "<i>$1</i>", $chaineMarkdown);
        return preg_replace("/(?<!\\\\)_([\w\W]*?[^\\\\])_/m", "<i>$1</i>", $chaineMarkdown);
    }

    /**
     * @param string $chaineMarkdown La chaine de caractère en markdown
     * @return string La chaine de caractère avec les listes
     * */
    private static function toBulletHtml(string $chaineMarkdown): string
    {
        $chaineMarkdown = preg_replace("/^[-*] (.*)\n?/m", "<li>$1</li>", $chaineMarkdown);
        return preg_replace("/^(<li>[^\n]*<\/li>)/m", "<ul>$1</ul>\n", $chaineMarkdown);
    }

    /**
     * @param string $chaineMarkdown La chaine de caractère en markdown
     * @return string La chaine de caractère sans le caractère d'échappement (sauf s'il est lui-même échappé)
     * */
    private static function toCleanHtml(string $chaineMarkdown): string
    {
        return preg_replace("/(?<!\\\\)\\\\/m", "", $chaineMarkdown);
    }
}