<?php

namespace App\SAE\Lib;

class Markdown {

    /**
     * @param string $markdownString La chaine de caractères en markdown à transformer en html
     * @return string La chaine de caractères en html
     */
    public static function toHtml(string $markdownString):string{
        $markdownString = htmlspecialchars($markdownString);
        $markdownString = self::toBulletHtml($markdownString); //
        $markdownString = self::toTitle6Html($markdownString); //
        $markdownString = self::toTitle5Html($markdownString); //
        $markdownString = self::toTitle4Html($markdownString); //
        $markdownString = self::toTitle3Html($markdownString); //
        $markdownString = self::toTitle2Html($markdownString); //
        $markdownString = self::toTitle1Html($markdownString); //
        $markdownString = self::toLineBreakHtml($markdownString);
        $markdownString = self::toBoldHtml($markdownString); //
        $markdownString = self::toUnderlineHtml($markdownString); //
        $markdownString = self::toItalicHtml($markdownString); //
        $markdownString = self::toLinkHtml($markdownString); //
        $markdownString = self::toCleanHtml($markdownString);

        return $markdownString;
    }

    /**
     * @param string $markdownString La chaine de caractère en markdown
     * @return string La chaine de caractère avec le texte en <b>gras</b>
     */
    private static function toBoldHtml(string $markdownString): string{
        return preg_replace("/(?<!\\\\)\*\*([\w\W]*?[^\\\\][*]*)\*\*/m", "<b>$1</b>", $markdownString);
    }

    /**
     * @param string $markdownString La chaine de caractère en markdown
     * @return string La chaine de caractère avec le texte en <u>souligné</u>
     */
    private static function toUnderlineHtml(string $markdownString): string{
        return preg_replace("/(?<!\\\\)__([\w\W]*?[^\\\\])__/m", "<u>$1</u>", $markdownString);
    }

    /**
     * @param string $markdownString La chaine de caractère en markdown
     * @return string La chaine de caractère avec les retours à la ligne
     * */
    private static function toLineBreakHtml(string $markdownString): string{
        $markdownString = preg_replace("/(^|<\/(?:h[1-6]|ul)>)\n?([^<]{2,}?)\n?(<(?:h[1-6]|ul)>|$)/", "$1<p>$2</p>$3", $markdownString); // crée des paragraphes

        $returnedString = "";
        while($returnedString != $markdownString){
            $markdownString = $returnedString == "" ? $markdownString : $returnedString;
            $returnedString =  preg_replace("/(?<=<h[1-6]>|<p>|\n|<br\/>)([^<]*?)\n([^<]*?)(?=\n|<\/h[1-6]>|<\/p>)/", "$1<br/>$2", $markdownString); //place des retours à la ligne dans des paragraphes ou titres
        }

        return preg_replace("/\n/", "", $returnedString); //place des retours à la ligne dans des paragraphes ou titres
    }

    /**
     * @param string $markdownString La chaine de caractère en markdown
     * @param int $titleSize La taille du titre
     * @return string La chaine de caractère avec les titres de taille $titleSize
     * */
    private static function toTitleHtml(string $markdownString, int $titleSize): string{
        return preg_replace("/^#{{$titleSize}}([^\n]*)/m", "<h$titleSize>$1</h$titleSize>", $markdownString);
    }

    /**
     * @param string $markdownString La chaine de caractère en markdown
     * @return string La chaine de caractère avec les <h1>titres de taille 1</h1>
     * */
    private static function toTitle1Html(string $markdownString): string{
        return self::toTitleHtml($markdownString, 1);
    }

    /**
     * @param string $markdownString La chaine de caractère en markdown
     * @return string La chaine de caractère avec les <h2>titres de taille 2</h2>
     * */
    private static function toTitle2Html(string $markdownString): string{
        return self::toTitleHtml($markdownString, 2);
    }

    /**
     * @param string $markdownString La chaine de caractère en markdown
     * @return string La chaine de caractère avec les <h3>titres de taille 3</h3>
     * */
    private static function toTitle3Html(string $markdownString): string{
        return self::toTitleHtml($markdownString, 3);
    }

    /**
     * @param string $markdownString La chaine de caractère en markdown
     * @return string La chaine de caractère avec les <h4>titres de taille 4</h4>
     * */
    private static function toTitle4Html(string $markdownString): string{
        return self::toTitleHtml($markdownString, 4);
    }

    /**
     * @param string $markdownString La chaine de caractère en markdown
     * @return string La chaine de caractère avec les <h5>titres de taille 5</h5>
     * */
    private static function toTitle5Html(string $markdownString): string{
        return self::toTitleHtml($markdownString, 5);
    }

    /**
     * @param string $markdownString La chaine de caractère en markdown
     * @return string La chaine de caractère avec les <h6>titres de taille 6</h6>
     * */
    private static function toTitle6Html(string $markdownString): string{
        return self::toTitleHtml($markdownString, 6);
    }

    /**
     * @param string $markdownString La chaine de caractère en markdown
     * @return string La chaine de caractère avec les <a href="">liens</a>
     * */
    private static function toLinkHtml(string $markdownString): string{
        return preg_replace("/(?<!\\\\)\[([^\[\]]*)]\(([^()]*)\)/m", "<a href=\"$2\" alt=\"$1\">$1</a>", $markdownString);
    }

    /**
     * @param string $markdownString La chaine de caractère en markdown
     * @return string La chaine de caractère avec les textes en <i>italique</i>
     * */
    private static function toItalicHtml(string $markdownString): string{
        $markdownString = preg_replace("/(?<!\\\\)\*([\w\W]*?[^\\\\])\*/m", "<i>$1</i>", $markdownString);
        return preg_replace("/(?<!\\\\)_([\w\W]*?[^\\\\])_/m", "<i>$1</i>", $markdownString);
    }

    /**
     * @param string $markdownString La chaine de caractère en markdown
     * @return string La chaine de caractère avec les listes
     * */
    private static function toBulletHtml(string $markdownString): string{
        $markdownString = preg_replace("/^[-*] (.*)\n?/m", "<li>$1</li>", $markdownString);
        return preg_replace("/^(<li>[^\n]*<\/li>)/m", "<ul>$1</ul>\n", $markdownString);
    }

    /**
     * @param string $markdownString La chaine de caractère en markdown
     * @return string La chaine de caractère sans le caractère d'échappement (sauf s'il est lui-même échappé)
     * */
    private static function toCleanHtml(string $markdownString): string{
        return preg_replace("/(?<!\\\\)\\\\/m", "", $markdownString);
    }
}