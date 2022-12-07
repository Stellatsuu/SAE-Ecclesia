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


class PropositionTest extends TestCase
{
    public function testAfficherProposition(){
        $proposition = Proposition::castIfNotNull((new PropositionRepository())->select("20001"));

        $this->assertEquals(20001, $proposition->getIdProposition());
        $this->assertEquals(10003, $proposition->getRedacteur());
        $this->assertEquals(10002, $proposition->getQuestion());

        $paragraphes = $proposition->getParagraphes();
        $this->assertNotEmpty($paragraphes);

        return $paragraphes;
    }

    /**
     * @depends testAfficherProposition
     */
    public function testParagraphes(array $paragraphes){
        $valeursParagraphe = array("idParagraphe" => 5001, "idProposition" => 20001, "idSection" => 4001, "contenu" => "blablabla");

        $this->assertContains($valeursParagraphe, $paragraphes);
    }
}