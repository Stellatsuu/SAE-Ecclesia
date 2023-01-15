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
use App\SAE\Model\HTTP\Session;
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
        $session = Session::getInstance();
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

        $savedData = $session->contient('savedData') ? $session->lire('savedData') : [];
        $session->supprimer('savedData');

        $dataQuestion = [
            //INFORMATIONS
            'idQuestion' => $question->getIdQuestion(),
            'titre' => $question->getTitre(),

            'description' => 
                $_POST['description'] ??
                $savedData['description'] ??
                $question->getDescription(),

            'tags' => 
                $_POST['tags'] ??
                $savedData['tags'] ??
                $question->getTags(),

            //PLAN
            'sections' => $savedData['sections'] ??
                $_POST['sections'] ??
                array_map(
                    function ($section) {
                        return [
                            'titre' => $section->getNomSection(),
                            'description' => $section->getDescriptionSection(),
                        ];
                    },
                    $question->getSections()
                ),

            //SYSTEME VOTE
            'systemeVote' => $_POST['systemeVote'] ??
                $savedData['systemeVote'] ??
                $question->getSystemeVote()->getNom(),

            //CALENDRIER
            'dateDebutRedaction' => 
                $_POST['dateDebutRedaction'] ??
                $savedData['dateDebutRedaction'] ??
                $question->getDateDebutRedaction()?->format('Y-m-d') ??
                (new DateTime("now"))->add(new DateInterval('P1D'))->format('Y-m-d'),

            'heureDebutRedaction' => 
                $_POST['heureDebutRedaction'] ??
                $savedData['heureDebutRedaction'] ??
                $question->getDateDebutRedaction()?->format('H:i') ??
                "08:00",

            'dateFinRedaction' => 
                $_POST['dateFinRedaction'] ??
                $savedData['dateFinRedaction'] ??
                $question->getDateFinRedaction()?->format('Y-m-d') ??
                (new DateTime("now"))->add(new DateInterval('P8D'))->format('Y-m-d'),

            'heureFinRedaction' => 
                $_POST['heureFinRedaction'] ??
                $savedData['heureFinRedaction'] ??
                $question->getDateFinRedaction()?->format('H:i') ??
                "08:00",

            'dateOuvertureVotes' => 
                $_POST['dateOuvertureVotes'] ??
                $savedData['dateOuvertureVotes'] ??
                $question->getDateOuvertureVotes()?->format('Y-m-d') ??
                (new DateTime("now"))->add(new DateInterval('P8D'))->format('Y-m-d'),

            'heureOuvertureVotes' => 
                $_POST['heureOuvertureVotes'] ??
                $savedData['heureOuvertureVotes'] ??
                $question->getDateOuvertureVotes()?->format('H:i') ??
                "08:00",

            'dateFermetureVotes' => 
                $_POST['dateFermetureVotes'] ??
                $savedData['dateFermetureVotes'] ??
                $question->getDateFermetureVotes()?->format('Y-m-d') ??
                (new DateTime("now"))->add(new DateInterval('P15D'))->format('Y-m-d'),

            'heureFermetureVotes' => 
                $_POST['heureFermetureVotes'] ??
                $savedData['heureFermetureVotes'] ??
                $question->getDateFermetureVotes()?->format('H:i') ??
                "08:00",

            //ROLES
            'redacteurs' => 
                $_POST['redacteurs'] ??
                $savedData['redacteurs'] ??
                array_map(
                    function ($redacteur) {
                        return $redacteur->getUsername();
                    },
                    $question->getRedacteurs()
                ),

            'votants' => 
                $_POST['votants'] ??
                $savedData['votants'] ??
                array_map(
                    function ($votant) {
                        return $votant->getUsername();
                    },
                    $question->getVotants()
                ),
        ];

        static::verifierDataQuestion($dataQuestion, $page);

        static::afficherVue("view.php", [
            "titrePage" => "Poser une question",
            "contenuPage" => "formulairePoserQuestion/$page.php",
            "dataQuestion" => $dataQuestion,
            "utilisateurs" => $utilisateurs,
        ]);
    }

    private static function verifierDataQuestion(array $dataQuestion, string $page = null)
    {
        $session = Session::getInstance();
        $session->enregistrer('savedData', $dataQuestion);

        $idQuestion = $dataQuestion['idQuestion'];

        $baseURL = "frontController.php?controller=question&action=afficherFormulairePoserQuestion&idQuestion=$idQuestion";
        $url = match ($page) {
            'plan' => "$baseURL&page=informations",
            'systeme_vote' => "$baseURL&page=plan",
            'calendrier' => "$baseURL&page=systeme_vote",
            'roles' => "$baseURL&page=calendrier",
            'confirmation' => "$baseURL&page=roles",
            default => $baseURL,
        };

        //INFORMATIONS
        if ($page == 'plan' || $page == null) {

            if (empty($dataQuestion['description'])) {
                static::error($url, "La description de la question ne peut pas être vide");
            } elseif (strlen($dataQuestion['description']) > 4000) {
                static::error($url, "La description de la question ne peut pas dépasser 4000 caractères");
            }

            if (!preg_match('/^(\{[a-zA-Z0-9]*\})|(\{[a-zA-Z0-9]+(,[a-zA-Z0-9]+)+\})$/', $dataQuestion['tags'])) {
                static::error($url, "Les tags de la question ne sont pas au bon format");
            }
        }

        //PLAN
        if ($page == 'systeme_vote' || $page == null) {

            $sections = $dataQuestion['sections'] ?? [];

            if (count($dataQuestion['sections']) == 0) {
                static::error($url, "La question doit avoir au moins une section");
            }

            foreach ($sections as $section) {
                if (empty($section['titre'])) {
                    static::error($url, "Le titre d'une section ne peut pas être vide");
                } elseif (strlen($section['titre']) > 50) {
                    static::error($url, "Le titre d'une section ne peut pas dépasser 50 caractères");
                }

                if (empty($section['description'])) {
                    static::error($url, "La description d'une section ne peut pas être vide");
                } elseif (strlen($section['description']) > 2000) {
                    static::error($url, "La description d'une section ne peut pas dépasser 2000 caractères");
                }
            }
        }

        //SYSTEME VOTE
        if($page == 'calendrier' || $page == null) {

            $valeursPossibles = [
                'majoritaire_a_un_tour',
                'approbation',
                'alternatif',
                'jugement_majoritaire',
            ];

            if (!in_array($dataQuestion['systemeVote'], $valeursPossibles)) {
                static::error($url, "Le système de vote n'est pas valide");
            }
        }

        //CALENDRIER
        if($page == 'roles' || $page == null) {

            $dateDebutRedaction = DateTime::createFromFormat('Y-m-d H:i', $dataQuestion['dateDebutRedaction'] . ' ' . $dataQuestion['heureDebutRedaction']);
            $dateFinRedaction = DateTime::createFromFormat('Y-m-d H:i', $dataQuestion['dateFinRedaction'] . ' ' . $dataQuestion['heureFinRedaction']);
            $dateOuvertureVotes = DateTime::createFromFormat('Y-m-d H:i', $dataQuestion['dateOuvertureVotes'] . ' ' . $dataQuestion['heureOuvertureVotes']);
            $dateFermetureVotes = DateTime::createFromFormat('Y-m-d H:i', $dataQuestion['dateFermetureVotes'] . ' ' . $dataQuestion['heureFermetureVotes']);
            
            //Vérification des dates
            //les dates doivent être dans l'ordre et ne pas être dans le passé
            $datesCohérentes = 
                $dateDebutRedaction < $dateFinRedaction &&
                $dateFinRedaction <= $dateOuvertureVotes &&
                $dateOuvertureVotes < $dateFermetureVotes &&
                $dateDebutRedaction > new DateTime();

            if (!$datesCohérentes) {
                static::error($url, "Les dates ne sont pas cohérentes");
            }
        }

        //ROLES
        if($page == 'confirmation' || $page == null) {

            $redacteurs = $dataQuestion['redacteurs'] ?? [];
            $votants = $dataQuestion['votants'] ?? [];
            
            $utilisateurs = (new UtilisateurRepository)->selectAll();
            $usernames = array_map(fn(Utilisateur $u) => $u->getUsername(), $utilisateurs);

            foreach ($redacteurs as $redacteur) {
                if (!in_array($redacteur, $usernames)) {
                    $redacteur = htmlspecialchars($redacteur);
                    static::error($url, "Le redacteur $redacteur n'existe pas");
                }
            }

            foreach ($votants as $votant) {
                if (!in_array($votant, $usernames)) {
                    $votant = htmlspecialchars($votant);
                    static::error($url, "Le votant $votant n'existe pas");
                }
            }

            if (count($redacteurs) == 0) {
                static::error($url, "La question doit avoir au moins un rédacteur");
            }

            if (count($votants) == 0) {
                static::error($url, "La question doit avoir au moins un votant");
            }
            
        }

        $session->supprimer('savedData');
    }


    public static function poserQuestion()
    {
        $username = ConnexionUtilisateur::getUsernameSiConnecte();
        $idQuestion = static::getIfSetAndNumeric('idQuestion', LMQ_URL);
        $question = Question::castIfNotNull((new QuestionRepository)->select($idQuestion));

        if ($question->getUsernameOrganisateur() != $username) {
            static::error(LMQ_URL, "Vous n'êtes pas l'organisateur de cette question");
        }

        $phase = $question->getPhase();
        if ($phase != Phase::Attente && $phase != Phase::NonRemplie) {
            static::error(LMQ_URL, "Vous ne pouvez pas poser la question depuis cette phase");
        }

        $dataQuestion = [
            'idQuestion' => $idQuestion,
            'description' => $_POST['description'],
            'tags' => $_POST['tags'],
            'sections' => $_POST['sections'],
            'systemeVote' => $_POST['systemeVote'],
            'dateDebutRedaction' => $_POST['dateDebutRedaction'],
            'heureDebutRedaction' => $_POST['heureDebutRedaction'],
            'dateFinRedaction' => $_POST['dateFinRedaction'],
            'heureFinRedaction' => $_POST['heureFinRedaction'],
            'dateOuvertureVotes' => $_POST['dateOuvertureVotes'],
            'heureOuvertureVotes' => $_POST['heureOuvertureVotes'],
            'dateFermetureVotes' => $_POST['dateFermetureVotes'],
            'heureFermetureVotes' => $_POST['heureFermetureVotes'],
            'redacteurs' => $_POST['redacteurs'],
            'votants' => $_POST['votants'],
        ];

        static::verifierDataQuestion($dataQuestion);

        $question->setDescription($dataQuestion['description']);
        $question->setSystemeVote(SystemeVoteFactory::createSystemeVote($dataQuestion['systemeVote'], $question));
        $question->setDateDebutRedaction(DateTime::createFromFormat('Y-m-d H:i', $dataQuestion['dateDebutRedaction'] . ' ' . $dataQuestion['heureDebutRedaction']));
        $question->setDateFinRedaction(DateTime::createFromFormat('Y-m-d H:i', $dataQuestion['dateFinRedaction'] . ' ' . $dataQuestion['heureFinRedaction']));
        $question->setDateOuvertureVotes(DateTime::createFromFormat('Y-m-d H:i', $dataQuestion['dateOuvertureVotes'] . ' ' . $dataQuestion['heureOuvertureVotes']));
        $question->setDateFermetureVotes(DateTime::createFromFormat('Y-m-d H:i', $dataQuestion['dateFermetureVotes'] . ' ' . $dataQuestion['heureFermetureVotes']));

        $sections = array_map(
            function ($section) use ($idQuestion) {
                return new Section(
                    -1,
                    $idQuestion,
                    $section['titre'],
                    $section['description'],
                );
            }, $dataQuestion['sections']
        );

        $redacteurs = array_map(
            function ($username) use ($idQuestion) {
                return (new UtilisateurRepository)->select($username);
            }, $dataQuestion['redacteurs']
        );

        $votants = array_map(
            function ($username) use ($idQuestion) {
                return (new UtilisateurRepository)->select($username);
            }, $dataQuestion['votants']
        );

        $question->setSections($sections);
        $question->setRedacteurs($redacteurs);
        $question->setVotants($votants);

        (new QuestionRepository)->update($question);
        static::message(LMQ_URL, "La question a été modifiée");
    }

    public static function passagePhaseRedaction()
    {
        $username = ConnexionUtilisateur::getUsernameSiConnecte();

        $idQuestion = static::getIfSetAndNumeric("idQuestion");

        $question = Question::castIfNotNull((new QuestionRepository)->select($idQuestion));

        if ($question->getUsernameOrganisateur() != $username) {
            static::error(LMQ_URL, "Vous n'êtes pas l'organisateur de cette question");
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
            static::error(LMQ_URL, "Vous n'êtes pas l'organisateur de cette question");
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
            } else if (substr($mot, 0, 1) == "@") {
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
