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

$fileName = __DIR__ . "/../tests/savedForms/" . $_GET['name'] . ".json";

echo "<pre>" . print_r($_POST, true) . "</pre>";

echo $fileName;
//save the form
$json = json_encode($_POST);
file_put_contents($fileName, $json);
