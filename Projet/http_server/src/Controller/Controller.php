<?php

namespace App\SAE\Controller;

class Controller
{

    private static ?string $message = NULL;
    private static ?string $messageType = "message";

    public static function afficherVue(string $cheminVue, array $parametres = []): void
    {
        $parametres['message'] = static::$message;
        $parametres['messageType'] = static::$messageType;
        extract($parametres); // Crée des variables à partir du tableau $parametres
        require __DIR__ . '/../view/' . $cheminVue;
    }

    /**
     * Affiche le message $message sur la page associée à $action
     */
    public static function message(string $action, string $message, array $parametres = []): void
    {
        static::$message = $message;
        if(empty($parametres)){
            static::$action();
        }else{
            static::$action($parametres);
        }
    }

    public static function error(string $action, string $message, array $parametres = []): void
    {
        static::$messageType = "errorMessage";
        static::message($action, $message, $parametres);
    }

    public static function afficherAccueil(): void
    {
        static::afficherVue('accueil.php');
    }

}
