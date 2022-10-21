<?php

namespace App\SAE\Config;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

class Conf
{

    static private array $databases = array(

        'hostname' => '178.18.244.32',

        'port' => '5432',

        'database' => 'sae3',

        'login' => 'alex',

        'password' => '9fZBgbju$eR9*rb9N3ciPq9hRbCRqu'
    );

    static public function getLogin(): string
    {
        return static::$databases['login'];
    }

    static public function getHostname(): string
    {
        return static::$databases['hostname'];
    }

    static public function getPort(): string
    {
        return static::$databases['port'];
    }

    static public function getDatabase(): string
    {
        return static::$databases['database'];
    }

    static public function getPassword(): string
    {
        return static::$databases['password'];
    }
}
