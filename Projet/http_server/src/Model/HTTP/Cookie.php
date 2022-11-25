<?php

namespace App\SAE\Model\HTTP;

class Cookie
{

    public static function enregistrer(string $cle, mixed $valeur, ?int $dureeExpiration = null): void
    {
        $serializedValeur = serialize($valeur);

        if ($dureeExpiration === null) {
            $expiration = 0;
        } else {
            $expiration = time() + $dureeExpiration;
        }

        setcookie($cle, $serializedValeur, $expiration);
    }

    public static function lire($cle): mixed
    {
        if (isset($_COOKIE[$cle])) {
            return unserialize($_COOKIE[$cle]);
        }
        return null;
    }

    public static function contient($cle): bool
    {
        return isset($_COOKIE[$cle]);
    }

    public static function supprimer($cle): void
    {
        setcookie($cle, '', time() - 3600);
    }
}
