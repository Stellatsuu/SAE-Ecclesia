<?php

use App\SAE\Controller\DemandeQuestionController;
use App\SAE\Controller\MainController;
use PHPUnit\Framework\TestCase;
use App\SAE\Model\Repository\PropositionRepository as PropositionRepository;
use App\SAE\Model\DataObject\Proposition as Proposition;
use App\SAE\Model\Repository\DatabaseConnection;

/* autoloader */

require_once __DIR__ . '/../src/Lib/Psr4AutoloaderClass.php';
$loader = new App\SAE\Lib\Psr4AutoloaderClass();
// register the base directories for the namespace prefix
$loader->addNamespace('App\SAE', __DIR__ . '/../src');
// register the autoloader
$loader->register();

if (!isset($_SESSION)) {
    $_SESSION = array();
}

class DemandeQuestionTest extends TestCase
{

    public function setUp(): void
    {
        MainController::setTesting(true);
        $_POST['idUtilisateur'] = 77777;
        MainController::seConnecter();

        MainController::clearLogFile();
        MainController::logToFile("Début des tests");

        MainController::resetDatabase();
    }


    public function testDemandeQuestionValide()
    {

        $_POST = json_decode(file_get_contents(__DIR__ . "/savedForms/demande_question/demande_question_valide.json"), true);
        DemandeQuestionController::demanderCreationQuestion();


        $sql = <<<SQL
        SELECT * 
            FROM demande_question 
            WHERE (titre_demande_question = :titre_demande_question 
            OR description_demande_question = :description_demande_question)
            AND id_organisateur = :id_utilisateur
        SQL;
        $pdo = DatabaseConnection::getPDO();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'titre_demande_question' => $_POST['titre'],
            'description_demande_question' => $_POST['description'],
            'id_utilisateur' => 77777
        ]);

        $this->assertEquals(1, $stmt->rowCount());
    }

    public function testDemandeQuestionInvalide1()
    {
        $_POST = json_decode(file_get_contents(__DIR__ . "/savedForms/demande_question/demande_question_invalide_1.json"), true);
        
        $this->expectException(\Exception::class);
        
        DemandeQuestionController::demanderCreationQuestion();
    }

    public function testDemandeQuestionInvalide2()
    {
        $_POST = json_decode(file_get_contents(__DIR__ . "/savedForms/demande_question/demande_question_invalide_2.json"), true);
        
        $this->expectException(\Exception::class);
        
        DemandeQuestionController::demanderCreationQuestion();
    }

    public function testDemandeQuestionInvalide3()
    {
        $_POST = json_decode(file_get_contents(__DIR__ . "/savedForms/demande_question/demande_question_invalide_3.json"), true);
        
        $this->expectException(\Exception::class);
        
        DemandeQuestionController::demanderCreationQuestion();
    }

    public function testDemandeQuestionInvalide4()
    {
        $_POST = json_decode(file_get_contents(__DIR__ . "/savedForms/demande_question/demande_question_invalide_4.json"), true);
        
        $this->expectException(\Exception::class);
        
        DemandeQuestionController::demanderCreationQuestion();
    }
}
