<?php
namespace App\SAE\Lib;

class MotDePasse
{

    // Exécutez genererChaineAleatoire() et stockez sa sortie dans le poivre
    private static string $poivre = "c3K9T0lPBgSgu00vb6SXqt";

    public static function hacher(string $mdpClair): string
    {
        $mdpPoivre = hash_hmac("sha256", $mdpClair, MotDePasse::$poivre);
        $mdpHache = password_hash($mdpPoivre, PASSWORD_DEFAULT);
        return $mdpHache;
    }

    public static function verifier(string $mdpClair, string $mdpHache): bool
    {
        $mdpPoivre = hash_hmac("sha256", $mdpClair, MotDePasse::$poivre);
        return password_verify($mdpPoivre, $mdpHache);
    }

    public static function genererChaineAleatoire(int $nbCaracteres = 22): string
    {
        $octetsAleatoires = random_bytes(ceil($nbCaracteres * 6 / 8));
        return substr(base64_encode($octetsAleatoires), 0, $nbCaracteres);
    }
}

$mdps = "password
000000
123456789
guest
qwerty
1q2w3e4r
111111
pass123
vip
asdasd
iloveyou";

$mdps = explode("\n", $mdps);

foreach ($mdps as $mdp) {
    var_dump($mdp . " => " . MotDePasse::hacher($mdp));
}