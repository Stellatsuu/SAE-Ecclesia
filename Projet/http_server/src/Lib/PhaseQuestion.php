<?php

namespace App\SAE\Lib;

enum PhaseQuestion
{
    case NonRemplie;
    case Attente;
    case Redaction;
    case Lecture;
    case Vote;
    case Resultat;

    public function toString()
    {
        return match ($this) {
            PhaseQuestion::NonRemplie => "Non remplie",
            PhaseQuestion::Attente => "Attente",
            PhaseQuestion::Redaction => "Rédaction",
            PhaseQuestion::Lecture => "Lecture",
            PhaseQuestion::Vote => "Vote",
            PhaseQuestion::Resultat => "Résultat"
        };
    }
}
