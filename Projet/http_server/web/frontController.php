<?php

require_once __DIR__ . '/../src/Lib/Psr4AutoloaderClass.php';

use App\SAE\Controller\Controller as Controller;





ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$loader = new App\SAE\Lib\Psr4AutoloaderClass();
// register the base directories for the namespace prefix
$loader->addNamespace('App\SAE', __DIR__ . '/../src');
// register the autoloader
$loader->register();

if (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    $action = "listerDemandesQuestion";
}

Controller::$action();



