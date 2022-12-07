<?php

namespace App\SAE\Model\DataObject;

use App\SAE\Controller\MainController;
use App\SAE\Model\Repository\UtilisateurRepository;

class DemandeQuestion extends AbstractDataObject
{

    private int $idQuestion;
    private string $titre;
    private string $description;

    private ?Utilisateur $organisateur;

    private int $idOrganisateur;

    public function __construct(int $idQuestion, string $titre, string $description, Utilisateur|int $organisateur)
    {
        $this->idQuestion = $idQuestion;
        $this->titre = $titre;
        $this->description = $description;


        if ($organisateur instanceof Utilisateur) {
            $this->organisateur = $organisateur;
            $this->idOrganisateur = $organisateur->getIdUtilisateur();
        } else {
            $this->organisateur = null;
            $this->idOrganisateur = $organisateur;
        }
    }

    //Respect du contrat

    public function formatTableau(): array
    {
        return [
            'titre_demande_question' => $this->titre,
            'description_demande_question' => $this->description,
            'id_organisateur' => $this->idOrganisateur
        ];
    }

    public function getValeurClePrimaire(): int
    {
        return $this->getIdQuestion();
    }

    // Getters
    
    public function getIdQuestion(): int
    {
        return $this->idQuestion;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getOrganisateur(): Utilisateur
    {
        if ($this->organisateur == null) {
            $this->organisateur = (new UtilisateurRepository())->select($this->idOrganisateur);
        }
        return $this->organisateur;
    }

    public function getIdOrganisateur(): int
    {
        return $this->idOrganisateur;
    }

    // Setters

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    // Caster

    public static function castIfNotNull($object, $errorUrl = "frontController.php", $errorMessage = "[OBJECT] n'existe pas") : DemandeQuestion
    {
        return static::castToClassIfNotNull($object, DemandeQuestion::class, $errorUrl, $errorMessage);
    }
    
}
