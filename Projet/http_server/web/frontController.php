<?php

require_once __DIR__ . '/../src/Lib/Psr4AutoloaderClass.php';

use App\SAE\Controller\DemandeQuestionController as DemandeQuestionController;




ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$loader = new App\SAE\Lib\Psr4AutoloaderClass();
// register the base directories for the namespace prefix
$loader->addNamespace('App\SAE', __DIR__ . '/../src');
// register the autoloader
$loader->register();

// On récupère le contrôleur et l'action à appeler
$controller = $_GET['controller'] ?? 'demandeQuestion';
$action = $_GET['action'] ?? 'listerDemandesQuestion';

$controller = 'App\\SAE\\Controller\\' . ucfirst($controller) . 'Controller';

if (class_exists($controller)) {
    if (method_exists($controller, $action)) {
        $controller::$action();
    } else {
        DemandeQuestionController::error('listerDemandesQuestion', "L'action $action n'existe pas");
    }
} else {
    DemandeQuestionController::error('listerDemandesQuestion', "Le contrôleur $controller n'existe pas");
}
