-- CLEAN DES TABLES

DROP TABLE Co_Auteur CASCADE;

DROP TABLE Votant CASCADE;

DROP TABLE Redacteur CASCADE;

DROP TABLE Section CASCADE;

DROP TABLE Question CASCADE;

DROP TABLE Demande_Question CASCADE;

DROP TABLE Administrateur CASCADE;

DROP TABLE Utilisateur CASCADE;

DROP TABLE Proposition CASCADE;

DROP TABLE Paragraphe CASCADE;

-- CREATION DES TABLES

CREATE TABLE Utilisateur (
    id_utilisateur serial,
    nom_utilisateur varchar(50) NOT NULL,
    prenom_utilisateur varchar(50) NOT NULL,
    mot_de_passe varchar(128) NOT NULL,
    CONSTRAINT pk_Utilisateur PRIMARY KEY (id_utilisateur)
);

CREATE TABLE Administrateur (
    id_administrateur serial,
    CONSTRAINT pk_Administrateur PRIMARY KEY (id_administrateur),
    CONSTRAINT fk_Administrateur FOREIGN KEY (id_administrateur) REFERENCES Utilisateur (id_utilisateur)
);

CREATE TABLE Demande_Question (
    id_demande_question serial,
    titre_demande_question varchar(100) NOT NULL,
    description_demande_question varchar(4000) NOT NULL,
    id_organisateur serial NOT NULL,
    CONSTRAINT pk_demande_question PRIMARY KEY (id_demande_question),
    CONSTRAINT fk_demande_question_organisateur FOREIGN KEY (id_organisateur) REFERENCES Utilisateur (id_utilisateur)
);

CREATE TABLE Question (
    id_question serial,
    titre_question varchar(100) NOT NULL,
    description_question varchar(4000) NOT NULL,
    id_organisateur serial NOT NULL,
    date_debut_redaction timestamp,
    date_fin_redaction timestamp,
    date_ouverture_votes timestamp,
    date_fermeture_votes timestamp,
    CONSTRAINT pk_question PRIMARY KEY (id_question),
    CONSTRAINT fk_question_organisateur FOREIGN KEY (id_organisateur) REFERENCES Utilisateur (id_utilisateur)
);

CREATE TABLE Section (
    id_section serial,
    id_question serial NOT NULL,
    nom_section varchar(50) NOT NULL,
    description_section varchar(2000) NOT NULL,
    CONSTRAINT pk_section PRIMARY KEY (id_section),
    CONSTRAINT fk_section_question FOREIGN KEY (id_question) REFERENCES Question (id_question)
);

CREATE TABLE Redacteur (
    id_redacteur serial,
    id_question serial,
    CONSTRAINT pk_Redacteur PRIMARY KEY (id_redacteur, id_question),
    CONSTRAINT fk_Redacteur FOREIGN KEY (id_redacteur) REFERENCES Utilisateur (id_utilisateur),
    CONSTRAINT fk_Redacteur_Question FOREIGN KEY (id_question) REFERENCES Question (id_question)
);

CREATE TABLE Votant (
    id_votant serial,
    id_question serial,
    CONSTRAINT pk_Votant PRIMARY KEY (id_votant, id_question),
    CONSTRAINT fk_Votant FOREIGN KEY (id_votant) REFERENCES Utilisateur (id_utilisateur),
    CONSTRAINT fk_Votant_Question FOREIGN KEY (id_question) REFERENCES Question (id_question)
);

CREATE TABLE Proposition (
    id_proposition serial,
    titre_proposition varchar(100) NOT NULL,
    id_responsable serial NOT NULL,
    id_question serial NOT NULL,
    CONSTRAINT pk_Proposition PRIMARY KEY (id_proposition),
    CONSTRAINT fk_Proposition_Responsable FOREIGN KEY (id_responsable) REFERENCES Utilisateur (id_utilisateur),
    CONSTRAINT fk_Proposition_Question FOREIGN KEY (id_question) REFERENCES Question (id_question),
    CONSTRAINT fk_Proposition_Responsable_Question FOREIGN KEY (id_responsable, id_question) REFERENCES Redacteur (id_redacteur, id_question)
);

CREATE TABLE Paragraphe (
    id_paragraphe serial,
    id_proposition serial NOT NULL,
    id_section serial NOT NULL,
    contenu_paragraphe TEXT NOT NULL,
    CONSTRAINT pk_Paragraphe PRIMARY KEY (id_paragraphe),
    CONSTRAINT fk_Paragraphe_Proposition FOREIGN KEY (id_proposition) REFERENCES Proposition (id_proposition),
    CONSTRAINT fk_Paragraphe_Section FOREIGN KEY (id_section) REFERENCES Section (id_section)
);


CREATE TABLE Co_Auteur(
   id_utilisateur serial,
   id_paragraphe serial,
   CONSTRAINT pk_Co_Auteur PRIMARY KEY(id_utilisateur, id_paragraphe),
   CONSTRAINT fk_Co_Auteur FOREIGN KEY(id_utilisateur) REFERENCES Utilisateur(id_utilisateur),
   CONSTRAINT fk_Co_Auteur_Paragraphe FOREIGN KEY(id_paragraphe) REFERENCES Paragraphe(id_paragraphe)
);

-- FONCTIONS, PROCEDURES ET TRIGGERS

CREATE OR REPLACE FUNCTION check_question_proposition_section ()
    RETURNS TRIGGER
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF (
        SELECT
            id_question
        FROM
            Proposition
        WHERE
            id_proposition = NEW.id_proposition) != (
    SELECT
        id_question
    FROM
        Section
    WHERE
        id_section = NEW.id_section) THEN
        RAISE EXCEPTION 'La question de la proposition et la question de la section ne sont pas les mêmes';
    END IF;
    RETURN NEW;
END;
$$;

CREATE TRIGGER check_question_proposition_section
    BEFORE INSERT OR UPDATE ON Paragraphe
    FOR EACH ROW
    EXECUTE PROCEDURE check_question_proposition_section ();

