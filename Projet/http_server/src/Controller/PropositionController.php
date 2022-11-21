<?php

namespace App\SAE\Controller;

use App\SAE\Model\DataObject\Paragraphe;
use App\SAE\Model\DataObject\Proposition;
use App\SAE\Model\DataObject\Question;
use App\SAE\Model\DataObject\Utilisateur;
use App\SAE\Model\Repository\ParagrapheRepository;
use App\SAE\Model\Repository\PropositionRepository;
use App\SAE\Model\Repository\QuestionRepository;
use App\SAE\Model\Repository\UtilisateurRepository;

class PropositionController extends Controller
{

    public static function afficherFormulaireEcrireProposition(array $parametre = null)
    {


        /* TODO: vérifier si l'utilisateur est co-auteur ou rédacteur (si co-auteur, pas le droit de toucher au titre) --> need authentification*/

        if (empty($_GET["idQuestion"])) {
            if (empty($parametre['proposition'])) {
                QuestionController::error("afficherAccueil", "Veuillez sélectionner la question pour laquelle vous souhaitez écrire une proposition.");
                return;
            } else {
                $idQuestion = $parametre['proposition']->getQuestion()->getIdQuestion();
            }
        } else {
            $idQuestion = intval($_GET['idQuestion']);
        }

        $question = (new QuestionRepository())->select($idQuestion);
        if (empty($question)) {
            QuestionController::error("afficherAccueil", "Veuillez sélectionner la question pour laquelle vous souhaitez écrire une proposition.");
            return;
        }

        $question = Question::toQuestion($question);

        // si la proposition existe déjà, on charge ses valeurs
        if (isset($_GET["idProposition"]) || !empty($parametre['proposition'])) {
            if (isset($_GET["idProposition"])) {
                $idProposition = intval($_GET['idProposition']);
                $proposition = (new PropositionRepository())->select($idProposition);
            } else {
                $proposition = $parametre['proposition'];
            }

            static::afficherVue('view.php', [
                "titrePage" => "Régiger une proposition",
                "contenuPage" => "formulaireEcrireProposition.php",
                "question" => $question,
                "proposition" => $proposition
            ]);
        } else {
            static::afficherVue('view.php', [
                "titrePage" => "Régiger une proposition",
                "contenuPage" => "formulaireEcrireProposition.php",
                "question" => $question
            ]);
        }
    }

    public static function ecrireProposition()
    {
        $error = "";

        if (empty($_POST["idQuestion"])) {
            static::error("afficherAccueil", "La question n'existe pas");
            return;
        }

        $question = Question::toQuestion((new QuestionRepository())->select($_POST['idQuestion']));
        if (empty($question)) {
            static::error("afficherAccueil", "La question n'existe pas");
            return;
        }

        $proposition = new Proposition(
            empty($_POST['idProposition']) ? 0 : intval($_POST['idProposition']),
            empty($_POST['titreProposition']) ? "" : $_POST['titreProposition'],
            (new UtilisateurRepository())->select($_POST['idResponsable']),
            $question,
            []
        );
        $paragraphes = [];

        foreach ($question->getSections() as $section) {
            $idParagraphe = 'section_' . $section->getIdSection();

            $paragraphes[] = new Paragraphe(empty($_POST[$idParagraphe . '_idParagraphe']) ? 0 : $_POST[$idParagraphe . '_idParagraphe'], $proposition->getIdProposition(), $section, empty($_POST[$idParagraphe]) ? "" : $_POST[$idParagraphe]);
        }

        $proposition->setParagraphes($paragraphes);



        // vérification existence des variables obligatoires
        if (empty($_POST['titreProposition'])) $error = "Vous devez préciser un nom pour votre proposition.";

        // vérification cohérence des données (taille, type)
        if (strlen($_POST['titreProposition']) > 100) $error = "Le titre ne doit pas faire plus de 100 caractères.";


        if (strlen($error) > 0) {
            $valeurs = [
                "proposition" => $proposition
            ];

            static::error("afficherFormulaireEcrireProposition", $error, $valeurs);
            return;
        }

        $estRedacteur = (new QuestionRepository())->estRedacteur($proposition->getQuestion()->getIdQuestion(), $_POST['idResponsable']);
        echo $estRedacteur;
        if (empty($_POST["idProposition"])) { // création d'une nouvelle proposition
            // vérification autorisations rédacteur
            if(!$estRedacteur){
                static::error("afficherAccueil",  "Vous devez être rédacteur sur cette question pour écrire une nouvelle proposition");
                return;
            }

            (new PropositionRepository())->insert($proposition);

            $idProposition = (new PropositionRepository())->selectByQuestionEtRedacteur($question, $_POST['idResponsable'])->getIdProposition();

            foreach ($paragraphes as $paragraphe) {
                $paragraphe->setIdProposition($idProposition);

                (new ParagrapheRepository())->insert($paragraphe);
            }
        } else { // édition d'une proposition déjà existante
            // vérification autorisation rédacteur ou co-auteur
            $estCoAuteur = $estRedacteur;

            if($estRedacteur){
                (new PropositionRepository())->update($proposition);
            }

            foreach ($paragraphes as $paragraphe) {
                if($estRedacteur || (new ParagrapheRepository())->estCoAuteur($paragraphe->getIdParagraphe(), $_POST['idResponsable'])){
                    (new ParagrapheRepository())->update($paragraphe);
                    $estCoAuteur = true;
                }
            }

            if(!$estCoAuteur){
                static::error("afficherAccueil",  "Vous devez être co-auteur ou rédacteur sur cette proposition pour la modifier.");
                return;
            }
        }

        QuestionController::listerMesQuestions();
    }

    public static function afficherFormulaireGererCoAuteurs()
    {

        $idProposition = intval($_GET['idProposition']);

        $proposition = Proposition::toProposition((new PropositionRepository())->select($idProposition));
        $utilisateursAutorises = (new UtilisateurRepository())->selectAll();

        $utilisateursAutorises = array_filter($utilisateursAutorises, function ($utilisateur) use ($proposition) {
            return $utilisateur->getIdUtilisateur() != $proposition->getRedacteur()->getIdUtilisateur();
        });

        $utilisateursAutorises = array_values($utilisateursAutorises);

        static::afficherVue('view.php', [
            "titrePage" => "Gérer les co-auteurs",
            "contenuPage" => "formulaireGererCoAuteurs.php",
            "proposition" => $proposition,
            "utilisateursAutorises" => $utilisateursAutorises,
            "coAuteurs" => $proposition->getCoAuteurs()
        ]);
    }

    public static function gererCoAuteurs()
    {

        $idProposition = intval($_POST['idProposition']);

        $proposition = Proposition::toProposition((new PropositionRepository())->select($idProposition));
        if (empty($proposition)) {
            DemandeQuestionController::error("afficherAccueil", "La proposition n'existe pas.");
            return;
        }

        $nbCoAuteurs = 1;
        $coAuteurs = [];

        while (isset($_POST['co_auteur' . $nbCoAuteurs])) {
            $coAuteur = Utilisateur::toUtilisateur((new UtilisateurRepository())->select($_POST['co_auteur' . $nbCoAuteurs]));
            if ($coAuteur && !in_array($coAuteur, $coAuteurs)) {
                $coAuteurs[] = $coAuteur;
            }
            $nbCoAuteurs++;
        }

        if (in_array($proposition->getRedacteur(), $coAuteurs)) {
            $_GET['idProposition'] = $idProposition;
            static::error("afficherFormulaireGererCoAuteurs", "Vous ne pouvez pas être co-auteur de votre propre proposition.");
        }

        (new PropositionRepository)->deleteCoAuteurs($proposition->getIdProposition());

        foreach ($coAuteurs as $coAuteur) {
            (new PropositionRepository)->addCoAuteurGlobal($proposition->getIdProposition(), $coAuteur->getIdUtilisateur());
        }

        static::message("afficherAccueil", "Les co-auteurs ont bien été modifiés.");
    }

}
