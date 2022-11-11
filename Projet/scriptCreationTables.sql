DROP TABLE Section CASCADE;
DROP TABLE Question CASCADE;
DROP TABLE Demande_Question CASCADE;
DROP TABLE Administrateur CASCADE;
DROP TABLE Utilisateur CASCADE;

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
    CONSTRAINT fk_Administrateur FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur (id_utilisateur)
);

CREATE TABLE Demande_Question (
    id_demande_question serial,
    titre_demande_question varchar(100) NOT NULL,
    description_demande_question text NOT NULL,
    id_organisateur serial NOT NULL,
    CONSTRAINT pk_demande_question PRIMARY KEY (id_question),
    CONSTRAINT fk_demande_question_organisateur FOREIGN KEY (id_organisateur) REFERENCES Utilisateur (id_utilisateur)
);

CREATE TABLE Question (
    id_question serial,
    titre_question varchar(100) NOT NULL,
    description_question text NOT NULL,
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
    description_section text NOT NULL,
    CONSTRAINT pk_section PRIMARY KEY (id_section),
    CONSTRAINT fk_section_question FOREIGN KEY (id_question) REFERENCES Question (id_question)
);

