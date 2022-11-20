<?php

use PHPUnit\Framework\TestCase;
use App\SAE\Model\Repository\PropositionRepository as PropositionRepository;
use App\SAE\Model\DataObject\Proposition as Proposition;

/* autoloader */
require_once __DIR__ . '/../src/Lib/Psr4AutoloaderClass.php';
$loader = new App\SAE\Lib\Psr4AutoloaderClass();
// register the base directories for the namespace prefix
$loader->addNamespace('App\SAE', __DIR__ . '/../src');
// register the autoloader
$loader->register();

class PropositionTest extends TestCase{

    public function testSomething(){

    }


}