<?php

namespace App\SAE\Model\Repository;

use App\SAE\Config\Conf as Conf;
use PDO;

class DatabaseConnection
{
    private static ?DatabaseConnection $instance = null;

    private PDO $pdo;

    public function __construct()
    {
        $hostname = Conf::getHostname();
        $port = Conf::getPort();
        $databaseName = Conf::getDatabase();
        $login = Conf::getLogin();
        $password = Conf::getPassword();
        $this->pdo = new PDO(
            "pgsql:host=$hostname;port=$port;dbname=$databaseName",
            $login,
            $password,
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getPdo(): PDO
    {
        return static::getInstance()->pdo;
    }

    private static function getInstance(): DatabaseConnection
    {
        // L'attribut statique $pdo s'obtient avec la syntaxe static::$pdo 
        // au lieu de $this->pdo pour un attribut non statique
        if (is_null(static::$instance))
            // Appel du constructeur
            static::$instance = new DatabaseConnection();
        return static::$instance;
    }
}
