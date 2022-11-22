<?php

namespace App\SAE\Lib;

enum PhaseQuestion {
    case NonRemplie;
    case Attente;
    case Redaction;
    case Lecture;
    case Vote;
    case Resultat;

    public function toString() {
        switch($this) {
            case PhaseQuestion::NonRemplie:
                return "Non remplie";
            case PhaseQuestion::Attente:
                return "Attente";
            case PhaseQuestion::Redaction:
                return "Rédaction";
            case PhaseQuestion::Lecture:
                return "Lecture";
            case PhaseQuestion::Vote:
                return "Vote";
            case PhaseQuestion::Resultat:
                return "Résultat";
        }
    }
}

