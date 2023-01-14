<?php

use App\SAE\Model\HTTP\Session;
use App\SAE\Lib\Markdown;
use App\SAE\Lib\PhotoProfil;

    $session = Session::getInstance();
    if($session->contient("username")) {
        $username = htmlspecialchars($session->lire("username"));
        $message = "Vous êtes actuellement connecté en tant que " . $username;
    } else {
        $message = "Vous n'êtes pas connecté";
    }


?>

<div id="accueil">
    <div id="leftSide">
        <img src="assets/images/ecclesia.svg" alt="logo du site"/>
    </div>
    <div  id="rightSide">
        <div class="panel">
            <h1>Derniers évènements :</h1>

            <div>
                <?php
                    foreach($questions as $question){
                        $idQuestion = $question["idQuestion"];
                        $titre = htmlspecialchars($question["titre"]);
                        $description = Markdown::toHtml($question["description"]);
                        $datePublication = htmlspecialchars($question["datePublication"]);
                        $phase = htmlspecialchars($question["phase"]);
                        $estAVous = $question['estAVous'];
                        $pfp = PhotoProfil::getBaliseImg($question['pfp'], "photo de profil", $estAVous ? "pfp--self" : "");
                        $nomUsuelOrganisateur = $estAVous ? "<strong>Vous</strong>" : htmlspecialchars($question['nomUsuelOrganisateur']);
                        $status = $question["statusQuestion"];

                        echo <<<HTML
                        <div class="question-compact">
                            <div class="question-compact__top">
                
                                <div class="question-compact__top__pfp user-tooltip">
                                    $pfp
                                    <div class="user-tooltip__text">
                                        $nomUsuelOrganisateur
                                    </div>
                                </div>
                
                                <a href="frontController.php?controller=question&action=afficherQuestion&idQuestion=$idQuestion">
                                    $titre
                                </a>
                                
                                <h2 class="statusQuestion">$status</h2>
                            </div>
                
                            <div class="question-compact__description markdown">
                                $description
                            </div>
                
                            <span class="question-compact__bottom">
                                <span>
                                $datePublication
                                </span>
                                <span>
                                    Phase : $phase
                                </span>
                            </span>
                        </div>
                    HTML;
                    }
                ?>
            </div>
        </div>
    </div>
</div>
