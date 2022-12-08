<?php

namespace App\SAE\Model\HTTP;

use App\SAE\Config\Conf as Conf;
use Exception;

class Session
{
    private static ?Session $instance = null;

    /**
     * @throws Exception
     */
    private function __construct()
    {
        if(!headers_sent()) {
            if(session_start() === false) {
                throw new Exception("Impossible de démarrer la session");
            }
        } else {
            $_SESSION = array();
        }

    }

    public static function getInstance(): Session
    {
        if (is_null(static::$instance))
            static::$instance = new Session();
        return static::$instance;
    }

    public function contient($name): bool
    {
        return isset($_SESSION[$name]);
    }

    public function enregistrer(string $name, mixed $value): void
    {
        $_SESSION[$name] = $value;
    }

    public function lire(string $name): mixed
    {
        return $_SESSION[$name];
    }

    public function supprimer($name): void
    {
        unset($_SESSION[$name]);
    }

    public function detruire(): void
    {
        session_unset();     // unset $_SESSION variable for the run-time
        session_destroy();   // destroy session data in storage
        Cookie::supprimer(session_name()); // deletes the session cookie
        // Il faudra reconstruire la session au prochain appel de getInstance()
        $instance = null;
    }

    public function verifierDerniereActivite()
    {
        $dureeExpiration = 60 * 30; // 30 minutes TODO: à mettre dans la conf
        if (isset($_SESSION['derniereActivite']) && (time() - $_SESSION['derniereActivite'] > ($dureeExpiration)))
            session_unset();     // unset $_SESSION variable for the run-time

        $_SESSION['derniereActivite'] = time(); // update last activity time stamp

    }
}
