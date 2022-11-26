<?php

namespace App\SAE\Model\SystemeVote;

use App\SAE\Model\DataObject\Question;

abstract class AbstractSystemeVote
{

    private Question $question;

    /**
     * @return array
     * Renvoie les propositions avec leur score, sous la forme d'un tableau associatif
     */
    public abstract function getResultats(): array;

    public abstract function getNom(): string;

    public function __construct(Question $question)
    {
        $this->question = $question;
    }
}
