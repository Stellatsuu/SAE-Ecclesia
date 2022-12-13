<?php

use App\SAE\Controller\DemandeQuestionController;
use App\SAE\Controller\MainController;
use App\SAE\Controller\UtilisateurController;
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

        $_POST['username'] = "test";
        $_POST['password'] = "test";
        UtilisateurController::seConnecter();

        MainController::clearLogFile();
        MainController::logToFile("Début des tests");

        MainController::resetDatabase();
    }

    /* Demander la création d'une question */

    public function testDemandeQuestionValide()
    {

        $_POST = json_decode(file_get_contents(__DIR__ . "/savedForms/demande_question/demande_question_valide.json"), true);
        DemandeQuestionController::demanderCreationQuestion();


        $sql = <<<SQL
        SELECT * 
            FROM demande_question 
            WHERE (titre_demande_question = :titre_demande_question 
            OR description_demande_question = :description_demande_question)
            AND username_organisateur = :username_utilisateur
        SQL;
        $pdo = DatabaseConnection::getPDO();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'titre_demande_question' => $_POST['titre'],
            'description_demande_question' => $_POST['description'],
            'username_utilisateur' => 'test'
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

    /* Accepter une demande de question */

    public function testAccepterDemandeQuestionValide(){
        $_POST = json_decode(file_get_contents(__DIR__ . "/savedForms/demande_question/demande_question_valide.json"), true);
        DemandeQuestionController::demanderCreationQuestion();

        $pdo = DatabaseConnection::getPDO();
        $idDemandeQuestionSql = <<<SQL
        
        SELECT id_demande_question 
        FROM demande_question 
        WHERE titre_demande_question = :titre_demande_question 
        AND username_organisateur = :username_utilisateur;
        SQL;

        $idDemandeQuestionStmt = $pdo->prepare($idDemandeQuestionSql);
        $idDemandeQuestionStmt->execute([
            "titre_demande_question" => $_POST['titre'],
            "username_utilisateur" => 'test'
        ]);
        $_POST['idQuestion'] = $idDemandeQuestionStmt->fetch()["id_demande_question"];
        DemandeQuestionController::accepterDemandeQuestion();

        $existeQuestionSql = <<<SQL
        SELECT * 
            FROM question 
            WHERE (titre_question = :titre_demande_question 
            AND description_question = :description_demande_question)
            AND username_organisateur = :username_utilisateur
        SQL;

        $existeQuestionStmt = $pdo->prepare($existeQuestionSql);
        $existeQuestionStmt->execute([
            'titre_demande_question' => $_POST['titre'],
            'description_demande_question' => $_POST['description'],
            'username_utilisateur' => 'test'
        ]);

        $existeDemandeQuestionSql = <<<SQL
        SELECT * 
            FROM demande_question 
            WHERE id_demande_question = :idQuestion
        SQL;

        $existeDemandeQuestionStmt = $pdo->prepare($existeDemandeQuestionSql);
        $existeDemandeQuestionStmt->execute([
            'idQuestion' => $_POST['idQuestion']
        ]);

        $this->assertEquals(1, $existeQuestionStmt->rowCount());
        $this->assertEquals(0, $existeDemandeQuestionStmt->rowCount());
    }

    // TODO: test user est admin

    /* Refuser une demande de question */

    public function testRefuserDemandeQuestionValide(){
        $_POST = json_decode(file_get_contents(__DIR__ . "/savedForms/demande_question/demande_question_valide.json"), true);
        DemandeQuestionController::demanderCreationQuestion();

        $pdo = DatabaseConnection::getPDO();
        $idDemandeQuestionSql = <<<SQL
        
        SELECT id_demande_question 
        FROM demande_question 
        WHERE titre_demande_question = :titre_demande_question 
        AND username_organisateur = :username_utilisateur;
        SQL;

        $idDemandeQuestionStmt = $pdo->prepare($idDemandeQuestionSql);
        $idDemandeQuestionStmt->execute([
            "titre_demande_question" => $_POST['titre'],
            "username_utilisateur" => 'test'
        ]);
        $_POST['idQuestion'] = $idDemandeQuestionStmt->fetch()["id_demande_question"];
        DemandeQuestionController::refuserDemandeQuestion();

        $existeQuestionSql = <<<SQL
        SELECT * 
            FROM question 
            WHERE (titre_question = :titre_demande_question 
            AND description_question = :description_demande_question)
            AND username_organisateur = :username_utilisateur
        SQL;

        $existeQuestionStmt = $pdo->prepare($existeQuestionSql);
        $existeQuestionStmt->execute([
            'titre_demande_question' => $_POST['titre'],
            'description_demande_question' => $_POST['description'],
            'username_utilisateur' => 'test'
        ]);

        $existeDemandeQuestionSql = <<<SQL
        SELECT * 
            FROM demande_question 
            WHERE id_demande_question = :idQuestion
        SQL;

        $existeDemandeQuestionStmt = $pdo->prepare($existeDemandeQuestionSql);
        $existeDemandeQuestionStmt->execute([
            'idQuestion' => $_POST['idQuestion']
        ]);

        $this->assertEquals(0, $existeQuestionStmt->rowCount());
        $this->assertEquals(0, $existeDemandeQuestionStmt->rowCount());
    }

    // TODO: test user est admin

    /* Afficher le formulaire demande question */

    public function testAfficherFormulaireDemandeQuestionValide(){
        $this->expectNotToPerformAssertions();

        DemandeQuestionController::afficherFormulaireDemandeQuestion();
    }

    public function testAfficherFormulaireDemandeQuestionUtilisateurNonConnecte(){
        UtilisateurController::seDeconnecter();

        $this->expectException(\Exception::class);

        DemandeQuestionController::afficherFormulaireDemandeQuestion();
    }

    /* Lister les demandes de question */

    public function testlisterDemandeQuestionValide(){
        $this->expectNotToPerformAssertions();

        DemandeQuestionController::listerDemandesQuestion();
    }

    // TODO: test user est admin
}
