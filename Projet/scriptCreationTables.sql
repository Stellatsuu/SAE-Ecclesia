
DROP TABLE Utilisateur CASCADE;

DROP TABLE Administrateur CASCADE;

DROP TABLE Demande_Question CASCADE;

DROP TABLE Question CASCADE;

DROP TABLE Section CASCADE;

CREATE TABLE Utilisateur (
    idUtilisateur serial,
    nom varchar(50) NOT NULL,
    prenom varchar(50) NOT NULL,
    motDePasse varchar(128) NOT NULL,
    CONSTRAINT pk_Utilisateur PRIMARY KEY (idUtilisateur)
);

CREATE TABLE Administrateur (
    idUtilisateur serial,
    CONSTRAINT pk_Administrateur PRIMARY KEY (idUtilisateur),
    CONSTRAINT fk_Administrateur FOREIGN KEY (idUtilisateur) REFERENCES Utilisateur (idUtilisateur)
);

CREATE TABLE Demande_Question (
    idQuestion serial,
    titre text NOT NULL,
    intitule text NOT NULL,
    estValide boolean NOT NULL DEFAULT FALSE,
    idUtilisateur serial, -- nom de l'organisateur de la question
    CONSTRAINT pk_demande_question PRIMARY KEY (idQuestion),
    CONSTRAINT fk_demande_question_organisateur FOREIGN KEY (idUtilisateur) REFERENCES Utilisateur (idUtilisateur)
);

CREATE TABLE Question (
    dateDebutRedaction timestamp NOT NULL,
    dateFinRedaction timestamp NOT NULL,
    dateOuvertureVotes timestamp NOT NULL,
    dateFermetureVotes timestamp NOT NULL,
    CONSTRAINT pk_question PRIMARY KEY (idQuestion),
    CONSTRAINT fk_question_organisateur FOREIGN KEY (idUtilisateur) REFERENCES Utilisateur (idUtilisateur)
)
INHERITS (
    Demande_Question
);

CREATE TABLE Section (
    idSection serial,
    idQuestion serial NOT NULL,
    nomSection varchar(50) NOT NULL,
    CONSTRAINT pk_section PRIMARY KEY (idSection),
    CONSTRAINT fk_section_question FOREIGN KEY (idQuestion) REFERENCES Question (idQuestion)
);

