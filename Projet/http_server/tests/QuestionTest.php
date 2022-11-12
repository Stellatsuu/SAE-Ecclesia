<?php

use PHPUnit\Framework\TestCase;
use App\SAE\Model\Repository\QuestionRepository as QuestionRepository;
use App\SAE\Model\DataObject\Question as Question;

/* autoloader */
require_once __DIR__ . '/../src/Lib/Psr4AutoloaderClass.php';
$loader = new App\SAE\Lib\Psr4AutoloaderClass();
// register the base directories for the namespace prefix
$loader->addNamespace('App\SAE', __DIR__ . '/../src');
// register the autoloader
$loader->register();

    class QuestionTest extends TestCase{

        public function testAfficherQuestion(){
            $question = Question::toQuestion((new QuestionRepository)->select(10001));

            $this->assertEquals("10001", $question->getIdQuestion());
            $this->assertEquals("Cryptographie", $question->getTitre());
            $this->assertEquals("Est-ce que le chiffrement symétrique est un bon chiffrement ?", $question->getDescription());
            $this->assertEquals(10004, $question->getOrganisateur()->getIdUtilisateur());
            $this->assertNull($question->getDateDebutRedaction());
            $this->assertNull($question->getDateFinRedaction());
            $this->assertNull($question->getDateOuvertureVotes());
            $this->assertNull($question->getDateFermetureVotes());
        }

        
    }