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
            array()
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->query("SET search_path TO ". Conf::getSchema() .";");
    }

    public static function getPdo(): PDO
    {
        return static::getInstance()->pdo;
    }

    private static function getInstance(): DatabaseConnection
    {
        if (is_null(static::$instance))
            static::$instance = new DatabaseConnection();

        return static::$instance;
    }
}
