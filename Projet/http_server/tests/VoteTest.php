<?php

use PHPUnit\Framework\TestCase;
use App\SAE\Model\Repository\VoteRepository;
use App\SAE\Model\DataObject\Vote;
use App\SAE\Model\DataObject\Proposition;
use App\SAE\Model\DataObject\Utilisateur;
use App\SAE\Model\Repository\PropositionRepository;
use App\SAE\Model\Repository\UtilisateurRepository;

/* autoloader */
require_once __DIR__ . '/../src/Lib/Psr4AutoloaderClass.php';
$loader = new App\SAE\Lib\Psr4AutoloaderClass();
// register the base directories for the namespace prefix
$loader->addNamespace('App\SAE', __DIR__ . '/../src');
// register the autoloader
$loader->register();


    //ATTENTION: ne fonctionne que si la proposition 1 existe et que l'utilisateur 10000 est votant dessus
    class VoteTest extends TestCase{

        public function testInsertionEtSelectVote(){

            $proposition = Proposition::toProposition((new PropositionRepository())->select(1));
            $votant = Utilisateur::toUtilisateur((new UtilisateurRepository())->select(10000));
            $valeur = 1;

            $vote = new Vote($proposition, $votant, $valeur);
            (new VoteRepository())->insert($vote);

            $res = (new VoteRepository())->select($votant->getIdUtilisateur(), $proposition->getIdProposition());
            $this->assertEquals($vote, $res);
        }

        public function testDeleteVote(){
            $proposition = Proposition::toProposition((new PropositionRepository())->select(1));
            $votant = Utilisateur::toUtilisateur((new UtilisateurRepository())->select(10000));
            $valeur = 1;

            $vote = new Vote($proposition, $votant, $valeur);
            (new VoteRepository())->delete($vote->getVotant()->getIdUtilisateur(), $vote->getProposition()->getIdProposition());

            $res = (new VoteRepository())->select($votant->getIdUtilisateur(), $proposition->getIdProposition());
            $this->assertEquals(null, $res);
        }


    }
    