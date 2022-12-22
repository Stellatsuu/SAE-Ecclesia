<?php

namespace App\SAE\Controller;

use App\SAE\Lib\MessageFlash;
use App\SAE\Lib\MotDePasse;
use App\SAE\Lib\PhotoProfil;
use App\SAE\Model\DataObject\Paragraphe;
use App\SAE\Model\DataObject\Proposition;
use App\SAE\Model\DataObject\Question;
use App\SAE\Model\DataObject\Section;
use App\SAE\Model\DataObject\Utilisateur;
use App\SAE\Model\HTTP\Session;
use App\SAE\Model\Repository\DatabaseConnection;
use App\SAE\Model\Repository\PropositionRepository;
use App\SAE\Model\Repository\QuestionRepository;
use App\SAE\Model\Repository\RedacteurRepository;
use App\SAE\Model\Repository\SectionRepository;
use App\SAE\Model\Repository\UtilisateurRepository;


class DebugController extends MainController
{
    private static array $words;

    private static function basicReset()
    {
        $pdo = DatabaseConnection::getPdo();
        $query1 = file_get_contents(__DIR__ . "/../../../scriptCreationTables.sql");
        $query2 = file_get_contents(__DIR__ . "/../../../jeuDeDonnées.sql");

        //set the pdo in warning mode
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);

        //get the drop statements from $query1
        $dropStatements = [];
        $statements = explode(";", $query1);
        foreach ($statements as $statement) {
            if (strpos($statement, "DROP") !== false) {
                $dropStatements[] = $statement;
            }
        }

        //remove the drop statements from $query1
        $query1 = str_replace($dropStatements, "", $query1);

        //execute the drop statements
        foreach ($dropStatements as $dropStatement) {
            $pdo->exec($dropStatement);
        }

        //set the pdo in exception mode
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $pdo->exec($query1);
        $pdo->exec($query2);
    }

    private static function randomWord()
    {
        return strtolower(static::$words[rand(0, count(static::$words) - 1)]);
    }

    private static function randomWords(int $minLength, int $maxLength)
    {

        do {
            $length = rand($minLength, $maxLength);
            $res = "";
            while (strlen($res) < $length) {
                $res .= static::randomWord() . " ";
            }
        } while (strlen($res) > $maxLength);

        return $res;
    }

    private static function insertRandomUsers(int $number)
    {
        if ($number <= 0) return;

        $words = static::$words;

        $usedImages = [];
        for ($i = 0; $i < $number; $i++) {
            $nom = ucfirst(static::randomWord());
            $prenom = ucfirst(static::randomWord());
            $username = strtolower($prenom . $nom . rand(0, 1000));
            $email = $username . "@gmail.com";

            $image = null;
            do {
                $image = file_get_contents("https://thispersondoesnotexist.com/image");
            } while (in_array($image, $usedImages));
            $usedImages[] = $image;

            $b64img = base64_encode($image);
            $image = PhotoProfil::convertirRedimensionnerRogner($b64img);

            $utilisateur = new Utilisateur(
                $username,
                $nom,
                $prenom,
                $email,
                MotDePasse::hacher("123456")
            );

            $utilisateur->setPhotoProfil($image);

            (new UtilisateurRepository())->insert($utilisateur);
        }
    }

    private static function insertRandomQuestions(int $number)
    {
        if ($number <= 0) return;

        $pdo = DatabaseConnection::getPdo();
        $sql = <<<SQL
        INSERT INTO Question(
            titre_question,
            description_question,
            username_organisateur,
            date_debut_redaction,
            date_fin_redaction,
            date_ouverture_votes,
            date_fermeture_votes)
        VALUES(
            :titre_question,
            :description_question,
            :username_organisateur,
            :date_debut_redaction,
            :date_fin_redaction,
            :date_ouverture_votes,
            :date_fermeture_votes)
        SQL;
        $stmt = $pdo->prepare($sql);

        $utilisateurs = array_values(array_filter((new UtilisateurRepository)->selectAll(), function ($utilisateur) {
            return preg_match("/[0-9]+$/", $utilisateur->getUsername());
        }));

        for ($i = 0; $i < $number; $i++) {
            $titre = static::randomWords(10, 100);
            $description = static::randomWords(100, 1000);

            $titre = ucfirst($titre);
            $description = ucfirst($description);

            $username = $utilisateurs[rand(0, count($utilisateurs) - 1)]->getUsername();
            $dateDebutRedaction = date("Y-m-d H:i:s", rand(0, time()));
            $dateFinRedaction = date("Y-m-d H:i:s", strtotime($dateDebutRedaction) + rand(0, 60 * 60 * 24 * 7));
            $dateOuvertureVotes = date("Y-m-d H:i:s", strtotime($dateFinRedaction) + rand(0, 60 * 60 * 24 * 7));
            $dateFermetureVotes = date("Y-m-d H:i:s", strtotime($dateOuvertureVotes) + rand(0, 60 * 60 * 24 * 7));

            $stmt->execute([
                "titre_question" => $titre,
                "description_question" => $description,
                "username_organisateur" => $username,
                "date_debut_redaction" => $dateDebutRedaction,
                "date_fin_redaction" => $dateFinRedaction,
                "date_ouverture_votes" => $dateOuvertureVotes,
                "date_fermeture_votes" => $dateFermetureVotes
            ]);
        }
    }

    private static function insertRandomSections(int $min, int $max)
    {

        $questions = (new QuestionRepository())->selectAll();
        //only keep questions from fake users
        $questions = array_values(array_filter($questions, function ($question) {
            return preg_match("/[0-9]+$/", $question->getUsernameOrganisateur());
        }));

        foreach ($questions as $question) {
            $nbSections = rand($min, $max);
            for ($i = 0; $i < $nbSections; $i++) {
                $titre = static::randomWords(10, 50);
                $description = static::randomWords(100, 1000);

                $titre = ucfirst($titre);
                $description = ucfirst($description);

                $section = new Section(
                    -1,
                    $question->getIdQuestion(),
                    $titre,
                    $description
                );

                (new SectionRepository())->insert($section);
            }
        }
    }

    private static function insertRandomRedacteurs(int $min, int $max)
    {

        $questions = (new QuestionRepository())->selectAll();
        //only keep questions from fake users
        $questions = array_values(array_filter($questions, function ($question) {
            return preg_match("/[0-9]+$/", $question->getUsernameOrganisateur());
        }));
        $utilisateurs = array_values(array_filter((new UtilisateurRepository)->selectAll(), function ($utilisateur) {
            return preg_match("/[0-9]+$/", $utilisateur->getUsername());
        }));

        foreach ($questions as $question) {
            $nbRedacteurs = rand($min, $max);
            $question = Question::castIfNotNull($question);
            $idQuestion = $question->getIdQuestion();

            $redacteurs = [];
            for ($i = 0; $i < $nbRedacteurs; $i++) {
                
                do {
                    $username = $utilisateurs[rand(0, count($utilisateurs) - 1)]->getUsername();
                } while (in_array($username, $redacteurs));

                $redacteurs[] = $username;
                (new RedacteurRepository())->insert($idQuestion, $username);
            }
        }
    }


    private static function insertRandomPropositions(float $chanceParRedacteur)
    {

        $questions = (new QuestionRepository())->selectAll();
        //only keep questions from fake users
        $questions = array_values(array_filter($questions, function ($question) {
            return preg_match("/[0-9]+$/", $question->getUsernameOrganisateur());
        }));

        foreach($questions as $question) {
            $question = Question::castIfNotNull($question);

            $usernamesRedacteur = (new RedacteurRepository())->selectAllByQuestion($question->getIdQuestion());

            foreach($usernamesRedacteur as $username) {
                if(rand(0, 100) > $chanceParRedacteur * 100) continue;

                $proposition = new Proposition(
                    -1,
                    static::randomWords(10, 100),
                    $username,
                    $question->getIdQuestion()
                );

                $paragraphes = [];
                foreach($question->getSections() as $section) {
                    $paragraphe = new Paragraphe(
                        -1,
                        -1,
                        $section->getIdSection(),
                        static::randomWords(100, 1000)
                    );
                    $paragraphes[] = $paragraphe;
                }

                $proposition->setParagraphes($paragraphes);

                (new PropositionRepository())->insert($proposition);
            }
        }
    }

    private static function updatePhotosProfil() {
        $utilisateurs = (new UtilisateurRepository())->selectAll();
        foreach($utilisateurs as $utilisateur) {
            $utilisateur = Utilisateur::castIfNotNull($utilisateur);
            
            $image = PhotoProfil::getRandomPhotoProfilParDefaut();
            $utilisateur->setPhotoProfil($image);
            (new UtilisateurRepository())->update($utilisateur);
        }
    }


    public static function resetDatabase(): void
    {
        $randomFakeUsers = isset($_GET["randomFakeUsers"]) ? $_GET["randomFakeUsers"] : 0;
        $randomFakeQuestions = isset($_GET["randomFakeQuestions"]) ? $_GET["randomFakeQuestions"] : 0;

        static::$words = explode("\n", file_get_contents(__DIR__ . "/../../../mots.txt"));

        static::basicReset();

        static::updatePhotosProfil();

        static::insertRandomUsers($randomFakeUsers);

        static::insertRandomQuestions($randomFakeQuestions);

        static::insertRandomSections(1, 4);

        static::insertRandomRedacteurs(3, 10);

        static::insertRandomPropositions(0.5);

        static::message(ACCUEIL_URL, "La base de données a été réinitialisée");
    }

    public static function logToFile(string $message): void
    {
        $date = date("Y-m-d H:i:s");
        $message = "$date : $message";
        file_put_contents(__DIR__ . "/../../../log.txt", $message . PHP_EOL, FILE_APPEND);
    }

    public static function clearLogFile(): void
    {
        file_put_contents(__DIR__ . "/../../../log.txt", "");
    }
}