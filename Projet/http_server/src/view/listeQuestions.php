<main>



<?php

foreach ($questions as $q) {
    echo("-" . $q->getQuestion() . "<br>");
    echo("-" . $q->getIntitule() . "<br>");
    echo("-" . $q->getOrganisateur()->getNom() . "<br>");
    echo("-" . $q->getOrganisateur()->getPrenom() . "<br>");
    if($q->getEstValide()) {
        echo("Question validée par l'admin<br>");
    } else {
        echo("Question pas encore validée par l'admin<br>");
    }
    echo("<br>");

}

?>

</main>