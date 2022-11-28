<?php

require_once __DIR__ . '/../src/Lib/Psr4AutoloaderClass.php';

use App\SAE\Controller\MainController;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$loader = new App\SAE\Lib\Psr4AutoloaderClass();
// register the base directories for the namespace prefix
$loader->addNamespace('App\SAE', __DIR__ . '/../src');
// register the autoloader
$loader->register();

// On récupère le contrôleur et l'action à appeler
$controller = $_GET['controller'] ?? 'main';
$action = $_GET['action'] ?? 'afficherAccueil';

$controller = 'App\\SAE\\Controller\\' . ucfirst($controller) . 'Controller';

if (class_exists($controller)) {
    if (method_exists($controller, $action)) {
        $controller::$action();
    } else {
        MainController::error('frontController.php', "L'action $action n'existe pas");
    }
} else {
    MainController::error('frontController.php', "Le contrôleur $controller n'existe pas");
}
