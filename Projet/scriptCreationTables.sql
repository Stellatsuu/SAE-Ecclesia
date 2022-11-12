DROP TABLE Votan CASCADE;
DROP TABLE Responsable CASCADE;
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

CREATE TABLE Votant (
    id_votant serial,
    id_question serial,
    CONSTRAINT pk_Votant PRIMARY KEY (id_votant, id_question),
    CONSTRAINT fk_Votant FOREIGN KEY (id_votant) REFERENCES Utilisateur (id_utilisateur),
    CONSTRAINT fk_Votant_Question FOREIGN KEY (id_question) REFERENCES Question (id_question)
);

CREATE TABLE Responsable (
    id_responsable serial,
    id_question serial,
    CONSTRAINT pk_Responsable PRIMARY KEY (id_responsable, id_question),
    CONSTRAINT fk_Responsable FOREIGN KEY (id_responsable) REFERENCES Utilisateur (id_utilisateur),
    CONSTRAINT fk_Responsable_Question FOREIGN KEY (id_question) REFERENCES Question (id_question)
);


