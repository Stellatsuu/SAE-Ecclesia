<?php

namespace App\SAE\Controller;

use App\SAE\Lib\MessageFlash;
use App\SAE\Lib\PhotoProfil;
use App\SAE\Model\HTTP\Session;
use App\SAE\Model\Repository\DatabaseConnection;
use App\SAE\Model\Repository\UtilisateurRepository;

/**
 * @var string URL de l'accueil
 */
const ACCUEIL_URL = "frontController.php";

/**
 * @var string URL de la liste des demandes de question
 */
const LDQ_URL = "frontController.php?controller=demandeQuestion&action=listerDemandesQuestion";

/**
 * @var string URL du formulaire de demande de question
 */
const AFDQ_URL = "frontController.php?controller=demandeQuestion&action=afficherFormulaireDemandeQuestion";
/**
 * @var string URL de la page "Lister mes questions"
 */
const LMQ_URL = "frontController.php?controller=question&action=listerMesQuestions";

class MainController
{

    private static bool $isTesting = false;

    public static function setTesting(bool $isTesting): void
    {
        static::$isTesting = $isTesting;
    }

    public static function afficherVue(string $cheminVue, array $parametres): void
    {
        if (static::$isTesting) return;

        extract($parametres); // Crée des variables à partir du tableau $parametres
        require __DIR__ . '/../view/' . $cheminVue;
    }

    public static function message(string $url, string $message): void
    {
        if (!static::$isTesting) {
            MessageFlash::ajouter("info", $message);
            static::redirect($url);
        } else {
            static::logToFile("info: " . $message);
        }
    }

    public static function error(string $url, string $message): void
    {
        if (!static::$isTesting) {
            MessageFlash::ajouter("error", $message);
            static::redirect($url);
        } else {
            static::logToFile("error: " . $message);
            throw new \Exception($message);
        }
    }

    public static function redirect(string $url): void
    {
        if (static::$isTesting) return;

        header("Location: $url");
        exit();
    }

    public static function afficherAccueil(): void
    {
        static::afficherVue("view.php", [
            "titrePage" => "Accueil",
            "contenuPage" => "accueil.php"
        ]);
    }

    public static function resetDatabase(): void
    {
        $randomFakeUsers = isset($_GET["randomFakeUsers"]) ? $_GET["randomFakeUsers"] : 0;
        $randomFakeQuestions = isset($_GET["randomFakeQuestions"]) ? $_GET["randomFakeQuestions"] : 0;

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

        $sql = <<<SQL
            INSERT INTO Utilisateur(
                username_utilisateur,
                nom_utilisateur, 
                prenom_utilisateur,
                email_utilisateur,
                photo_profil, 
                mdp_hashed)
            VALUES(
                :username_utilisateur,
                :nom_utilisateur, 
                :prenom_utilisateur,
                :email_utilisateur,
                :photo_profil, 
                :mdp_hashed)
        SQL;

        $stmt = $pdo->prepare($sql);

        $words = explode("\n", file_get_contents(__DIR__ . "/../../../mots.txt"));
        
        for ($i = 0; $i < $randomFakeUsers; $i++) {
            $nom = ucfirst(strtolower($words[rand(0, count($words) - 1)]));
            $prenom = ucfirst(strtolower($words[rand(0, count($words) - 1)]));
            $username = strtolower($prenom . $nom . rand(0, 1000));
            $email = $username . "@gmail.com";

            $values = [
                "username_utilisateur" => $username,
                "nom_utilisateur" => $nom,
                "prenom_utilisateur" => $prenom,
                "email_utilisateur" => $email,
                "photo_profil" => null,
                "mdp_hashed" => ""
            ];

            $stmt->execute($values);
        }

                
        $words = explode("\n", file_get_contents(__DIR__ . "/../../../mots.txt"));
        $utilisateurs = (new UtilisateurRepository())->selectAll();
        //ne garder que les utilisateurs qui ont des nombres à la fin de leur username
        $utilisateurs = array_filter($utilisateurs, function($utilisateur) {
            return preg_match("/[0-9]+$/", $utilisateur->getUsername());
        });
        $utilisateurs = array_values($utilisateurs);

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

        for ($i = 0; $i < $randomFakeQuestions; $i++) {
            $titre = "";
            $description = "";

            while(strlen($titre) > 100 || strlen($titre) < 10) {
                $titre = "";
                $nbMots = rand(1, 10);
                for ($j = 0; $j < $nbMots; $j++) {
                    $titre .= $words[rand(0, count($words) - 1)] . " ";
                }
            }

            while(strlen($description) > 1000 || strlen($description) < 100) {
                $description = "";
                $nbMots = rand(1, 100);
                for ($j = 0; $j < $nbMots; $j++) {
                    $description .= $words[rand(0, count($words) - 1)] . " ";
                }
            }

            $titre = ucfirst(strtolower($titre));
            $description = ucfirst(strtolower($description));

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

        //give each user a profile picture
        $images = [];
        foreach($utilisateurs as $utilisateur) {
            //get a random image from thispersondoesnotexist.com, but make sure no image is used twice
            $image = null;
            do {
                $image = file_get_contents("https://thispersondoesnotexist.com/image");
            } while(in_array($image, $images));
            $images[] = $image;

            $b64img = base64_encode($image);
            $image = PhotoProfil::convertirRedimensionnerRogner($b64img);
            $utilisateur->setPhotoProfil($image);
            (new UtilisateurRepository())->update($utilisateur);
        }
            
        static::message(ACCUEIL_URL, "La base de données a été réinitialisée");
    }

    protected static function getIfSet(string $parametre, string $errorUrl = ACCUEIL_URL, string $errorMessage = "[PARAMETRE] non rempli"): string
    {
        $errorMessage = str_replace("[PARAMETRE]", $parametre, $errorMessage);
        if (!isset($_GET[$parametre])) {
            if (!isset($_POST[$parametre])) {
                static::error($errorUrl, $errorMessage);
            } else {
                return $_POST[$parametre];
            }
        } else {
            return $_GET[$parametre];
        }
    }

    protected static function getIfSetAndNumeric(string $parametre, string $errorUrl = ACCUEIL_URL, string $errorMessage = "[PARAMETRE] non rempli"): int
    {
        $valeur = static::getIfSet($parametre, $errorUrl, $errorMessage);
        if (!is_numeric($valeur)) {
            static::error($errorUrl, "$parametre doit être un nombre");
        }
        return (int) $valeur;
    }

    protected static function getIfSetAndNotEmpty(string $parametre, string $errorUrl = ACCUEIL_URL, string $errorMessage = "[PARAMETRE] non rempli"): string
    {
        $valeur = static::getIfSet($parametre, $errorUrl, $errorMessage);
        if (empty($valeur)) {
            static::error($errorUrl, "$parametre ne peut pas être vide");
        }
        return $valeur;
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
