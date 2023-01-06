<?php

namespace App\SAE\Model\DataObject;

use App\SAE\Model\Repository\UtilisateurRepository;

class DemandeQuestion extends AbstractDataObject
{

    private int $idQuestion;
    private string $titre;
    private string $description;

    private ?Utilisateur $organisateur;

    private string $usernameOrganisateur;

    public function __construct(int $idQuestion, string $titre, string $description, Utilisateur|string $organisateur)
    {
        $this->idQuestion = $idQuestion;
        $this->titre = $titre;
        $this->description = $description;


        if ($organisateur instanceof Utilisateur) {
            $this->organisateur = $organisateur;
            $this->usernameOrganisateur = $organisateur->getUsername();
        } else {
            $this->organisateur = null;
            $this->usernameOrganisateur = $organisateur;
        }
    }

    //Respect du contrat

    public function formatTableau(): array
    {
        return [
            'titre_demande_question' => $this->titre,
            'description_demande_question' => $this->description,
            'username_organisateur' => $this->usernameOrganisateur
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
            $this->organisateur = (new UtilisateurRepository())->select($this->usernameOrganisateur);
        }
        return $this->organisateur;
    }

    public function getUsernameOrganisateur(): string
    {
        return $this->usernameOrganisateur;
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
