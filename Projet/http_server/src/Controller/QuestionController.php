<?php

namespace App\SAE\Controller;

use App\SAE\Lib\ConnexionUtilisateur;
use App\SAE\Lib\MessageFlash;
use App\SAE\Model\Repository\QuestionRepository as QuestionRepository;
use App\SAE\Model\DataObject\Question as Question;
use App\SAE\Model\DataObject\Section;
use App\SAE\Model\DataObject\Utilisateur;
use App\SAE\Model\Repository\UtilisateurRepository as UtilisateurRepository;
use App\SAE\Lib\PhaseQuestion as Phase;
use App\SAE\Model\DataObject\Proposition;
use App\SAE\Model\Repository\PropositionRepository as PropositionRepository;
use App\SAE\Model\Repository\RedacteurRepository;
use App\SAE\Model\Repository\VotantRepository;
use App\SAE\Model\SystemeVote\SystemeVoteFactory;
use DateTime;
use DateInterval;

class QuestionController extends MainController
{

    public static function afficherFormulairePoserQuestion(): void
    {
        $username = ConnexionUtilisateur::getUsernameSiConnecte();

        $idQuestion = static::getIfSetAndNumeric("idQuestion");
        $question = Question::castIfNotNull((new QuestionRepository)->select($idQuestion));
        if ($question->getUsernameOrganisateur() != $username) {
            static::error(LQ_URL, "Vous n'êtes pas l'organisateur de cette question");
        }
        $page = $_GET['page'] ?? 'informations';
        if (!in_array($page, ['informations', 'plan', 'systeme_vote', 'calendrier', 'roles', 'confirmation'])) {
            static::error(ACCUEIL_URL, "Erreur de navigation");
        }

        if ($page == 'roles') {
            $utilisateurs = (new UtilisateurRepository)->selectAll();
        } else {
            $utilisateurs = [];
        }

        $dataQuestion = [
            //INFORMATIONS
            'idQuestion' => $question->getIdQuestion(),
            'titre' => $question->getTitre(),
            'description' => $_POST['description'] ?? $question->getDescription(),
            'tags' => $_POST['tags'] ?? $question->getTags(),
            //PLAN
            'sections' => $_POST['sections'] ?? array_map(
                function ($section) {
                    return [
                        'titre' => $section->getNomSection(),
                        'description' => $section->getDescriptionSection(),
                    ];
                },
                $question->getSections()
            ),
            //SYSTEME VOTE
            'systemeVote' => $_POST['systemeVote'] ?? $question->getSystemeVote()->getNom(),
            //CALENDRIER
            'dateDebutRedaction' => $_POST['dateDebutRedaction'] ??
                $question->getDateDebutRedaction()?->format('Y-m-d') ??
                (new DateTime("now"))->add(new DateInterval('P1D'))->format('Y-m-d'),

            'heureDebutRedaction' => $_POST['heureDebutRedaction'] ??
                $question->getDateDebutRedaction()?->format('H:i') ??
                "08:00",

            'dateFinRedaction' => $_POST['dateFinRedaction'] ??
                $question->getDateFinRedaction()?->format('Y-m-d') ??
                (new DateTime("now"))->add(new DateInterval('P8D'))->format('Y-m-d'),

            'heureFinRedaction' => $_POST['heureFinRedaction'] ??
                $question->getDateFinRedaction()?->format('H:i') ??
                "08:00",

            'dateOuvertureVotes' => $_POST['dateOuvertureVotes'] ??
                $question->getDateOuvertureVotes()?->format('Y-m-d') ??
                (new DateTime("now"))->add(new DateInterval('P8D'))->format('Y-m-d'),

            'heureOuvertureVotes' => $_POST['heureOuvertureVotes'] ??
                $question->getDateOuvertureVotes()?->format('H:i') ??
                "08:00",

            'dateFermetureVotes' => $_POST['dateFermetureVotes'] ??
                $question->getDateFermetureVotes()?->format('Y-m-d') ??
                (new DateTime("now"))->add(new DateInterval('P15D'))->format('Y-m-d'),

            'heureFermetureVotes' => $_POST['heureFermetureVotes'] ??
                $question->getDateFermetureVotes()?->format('H:i') ??
                "08:00",

            //ROLES
            'redacteurs' => $_POST['redacteurs'] ??
                array_map(
                    function ($redacteur) {
                        return $redacteur->getUsername();
                    },
                    $question->getRedacteurs()
                ),

            'votants' => $_POST['votants'] ??
                array_map(
                    function ($votant) {
                        return $votant->getUsername();
                    },
                    $question->getVotants()
                ),
        ];

        static::afficherVue("view.php", [
            "titrePage" => "Poser une question",
            "contenuPage" => "formulairePoserQuestion/$page.php",
            "dataQuestion" => $dataQuestion,
            "utilisateurs" => $utilisateurs,
        ]);
    }

    public static function poserQuestion()
    {
    }

    public static function passagePhaseRedaction()
    {
        $username = ConnexionUtilisateur::getUsernameSiConnecte();

        $idQuestion = static::getIfSetAndNumeric("idQuestion");

        $question = Question::castIfNotNull((new QuestionRepository)->select($idQuestion));

        if ($question->getUsernameOrganisateur() != $username) {
            static::error(LQ_URL, "Vous n'êtes pas l'organisateur de cette question");
            return;
        }

        $phase = $question->getPhase();
        if ($phase != Phase::Attente) {
            static::error(LMQ_URL, "Vous ne pouvez pas passer à la phase de rédaction depuis cette phase");
            return;
        }

        $question->setDateDebutRedaction(new DateTime("now"));
        (new QuestionRepository)->updateSansTablesAssociees($question);
        static::message(LMQ_URL, "La question est maintenant en phase de rédaction");
    }

    public static function passagePhaseVote()
    {
        $username = ConnexionUtilisateur::getUsernameSiConnecte();

        $idQuestion = static::getIfSetAndNumeric("idQuestion");

        $question = Question::castIfNotNull((new QuestionRepository)->select($idQuestion));

        if ($question->getUsernameOrganisateur() != $username) {
            static::error(LQ_URL, "Vous n'êtes pas l'organisateur de cette question");
            return;
        }

        $phase = $question->getPhase();
        if ($phase != Phase::Redaction && $phase != Phase::Lecture) {
            static::error(LMQ_URL, "La question n'est pas en phase de rédaction ou de vote");
            return;
        }

        if ($phase == Phase::Redaction)
            $question->setDateFinRedaction(new DateTime("now"));

        $question->setDateOuvertureVotes(new DateTime("now"));

        (new QuestionRepository)->updateSansTablesAssociees($question);
        static::message(LMQ_URL, "La question est maintenant en phase de vote");
    }

    public static function passagePhaseResultats()
    {
        $username = ConnexionUtilisateur::getUsernameSiConnecte();

        $idQuestion = static::getIfSetAndNumeric("idQuestion");

        $question = Question::castIfNotNull((new QuestionRepository)->select($idQuestion));

        if ($question->getUsernameOrganisateur() != $username) {
            static::error(LQ_URL, "Vous n'êtes pas l'organisateur de cette question");
            return;
        }

        $phase = $question->getPhase();
        if ($phase != Phase::Vote) {
            static::error(LMQ_URL, "La question n'est pas en phase de vote");
            return;
        }

        $question->setDateFermetureVotes(new DateTime("now"));
        (new QuestionRepository)->updateSansTablesAssociees($question);
        static::message(LMQ_URL, "La question est terminée. Vous pouvez maintenant voir les résultats");
    }

    public static function afficherResultats()
    {
        $idQuestion = static::getIfSetAndNumeric("idQuestion");

        $question = Question::castIfNotNull((new QuestionRepository)->select($idQuestion));

        $phase = $question->getPhase();
        if ($phase != Phase::Resultat) {
            static::error(LQ_URL, "La question n'est pas terminée. Vous ne pouvez pas encore voir les résultats");
            return;
        }

        $dataQuestion = [
            "titre" => $question->getTitre(),
            "description" => $question->getDescription(),
            "nomUsuelOrganisateur" => $question->getOrganisateur()->getNomUsuel(),
            "resultats" => $question->getSystemeVote()->afficherResultats()
        ];

        static::afficherVue("view.php", [
            "titrePage" => "Résultats",
            "contenuPage" => "afficherResultats.php",
            "dataQuestion" => $dataQuestion
        ]);
    }

    public static function listerQuestions()
    {
        $username = ConnexionUtilisateur::getUsername() ?: "";

        $nbQuestionsParPage = 12;
        $query = isset($_GET["query"]) ? strtolower($_GET["query"]) : "";
        $motsClesEtTagsEtUsername = explode(" ", $query) ?: [];
        $motsCles = [];
        $tags = [];
        $usernames = [];

        foreach ($motsClesEtTagsEtUsername as $mot) {
            if (substr($mot, 0, 1) == "#") {
                $tags[] = substr($mot, 1);
            } else if(substr($mot, 0, 1) == "@"){
                $usernames[] = substr($mot, 1);
            } else {
                $motsCles[] = $mot;
            }
        }
        $tags = array_filter($tags, function ($t) {
            return $t != "";
        });

        $usernames = array_filter($usernames, function ($t) {
            return $t != "";
        });

        $filtres = [];
        if (isset($_GET["f_non_remplie"])) $filtres[] = "non_remplie";
        if (isset($_GET["f_attente"])) $filtres[] = "attente";
        if (isset($_GET["f_lecture"])) $filtres[] = "lecture";
        if (isset($_GET["f_redaction"])) $filtres[] = "redaction";
        if (isset($_GET["f_vote"])) $filtres[] = "vote";
        if (isset($_GET["f_resultat"])) $filtres[] = "resultat";
        if (isset($_GET["f_redacteur"])) $filtres[] = "redacteur";
        if (isset($_GET["f_coauteur"])) $filtres[] = "coauteur";
        if (isset($_GET["f_votant"])) $filtres[] = "votant";

        if (isset($_GET["f_mq"])) $filtres[] = "mq";


        $nbPages = ceil((new QuestionRepository())->countAllListerQuestion($motsCles, $tags, $filtres, $usernames) / $nbQuestionsParPage) ?: 1;
        $page = isset($_GET["page"]) && is_numeric($_GET["page"]) && $_GET["page"] > 0 && $_GET["page"] <= $nbPages ? $_GET["page"] : 1;

        $offset = ($page - 1) * $nbQuestionsParPage;

        $questions = (new QuestionRepository())->selectAllListerQuestions($nbQuestionsParPage, $offset, $motsCles, $tags, $filtres, $usernames);

        $dataQuestions = array_map(function ($question) use ($username) {
            $question = Question::castIfNotNull($question);
            return [
                "idQuestion" => $question->getIdQuestion(),
                "titre" => $question->getTitre(),
                "description" => $question->getDescription(),
                "datePublication" => $question->getDateDebutRedaction() ? $question->getDateDebutRedaction()->format("d/m/Y") : "Non Publiée",
                "phase" => $question->getPhase()->toString(),
                "nomUsuelOrganisateur" => $question->getOrganisateur()->getNomUsuel(),
                "pfp" => $question->getOrganisateur()->getPhotoProfil(),
                "estAVous" => $username == $question->getUsernameOrganisateur(),
            ];
        }, $questions);

        static::afficherVue("view.php", [
            "titrePage" => "Liste des questions",
            "contenuPage" => "listeQuestions.php",
            "dataQuestions" => $dataQuestions,
            "estConnecte" => ConnexionUtilisateur::estConnecte(),
            "page" => $page,
            "nbPages" => $nbPages,
            "query" => $query,
            "filtres" => $filtres,
        ]);
    }

    public static function afficherQuestion()
    {
        $idQuestion = static::getIfSetAndNumeric("idQuestion");
        $question = Question::castIfNotNull((new QuestionRepository)->select($idQuestion));
        $propositions = (new PropositionRepository())->selectAllByQuestion($idQuestion);
        $phase = $question->getPhase();

        $username = ConnexionUtilisateur::estConnecte() ? ConnexionUtilisateur::getUsername() : "";

        $estOrganisateur = $question->getUsernameOrganisateur() == $username;
        $estRedacteur = (new RedacteurRepository)->existsForQuestion($idQuestion, $username);
        $estVotant = (new VotantRepository)->existsForQuestion($idQuestion, $username);

        $peutEditer = $estOrganisateur && ($phase == Phase::NonRemplie || $phase == Phase::Attente);
        $peutChangerPhase = $estOrganisateur && $phase != Phase::Resultat && $phase != Phase::NonRemplie;
        $peutEcrireProposition = $phase == Phase::Redaction && $estRedacteur && (new PropositionRepository)->selectByQuestionEtResponsable($idQuestion, $username) == null;
        $peutVoter = $estVotant && $phase == Phase::Vote;

        $dataQuestion = [
            "idQuestion" => $question->getIdQuestion(),
            "titre" => $question->getTitre(),
            "description" => $question->getDescription(),
            "nomUsuelOrga" => $question->getOrganisateur()->getNomUsuel(),
            "phase" => $question->getPhase(),
            "nomSystemeVote" => $question->getSystemeVote()->getNomComplet(),

            "sections" => array_map(function ($section) {
                return [
                    "titre" => $section->getNomSection(),
                    "description" => $section->getDescriptionSection()
                ];
            }, $question->getSections()),

            "propositions" => array_map(function ($proposition) use ($username) {
                return [
                    "idProposition" => $proposition->getIdProposition(),
                    "titre" => $proposition->getTitreProposition(),
                    "nomUsuelResp" => $proposition->getResponsable()->getNomUsuel(),
                    "pfp" => $proposition->getResponsable()->getPhotoProfil(),
                    "estAVous" => ($proposition->getUsernameResponsable() == $username)
                ];
            }, $propositions)
        ];

        static::afficherVue("view.php", [
            "titrePage" => "Question",
            "contenuPage" => "afficherQuestion.php",
            "dataQuestion" => $dataQuestion,
            "peutEditer" => $peutEditer,
            "peutChangerPhase" => $peutChangerPhase,
            "peutEcrireProposition" => $peutEcrireProposition,
            "peutVoter" => $peutVoter,
        ]);
    }
}
