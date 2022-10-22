DROP TABLE Question;
DROP TABLE Administrateur;
DROP TABLE Utilisateur;

CREATE TABLE Utilisateur(
    idUtilisateur SERIAL,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    motDePasse VARCHAR(128) NOT NULL,
    CONSTRAINT pk_Utilisateur PRIMARY KEY(idUtilisateur)
);

CREATE TABLE Administrateur(
    idUtilisateur SERIAL,
    CONSTRAINT pk_Administrateur PRIMARY KEY(idUtilisateur),
    CONSTRAINT fk_Administrateur FOREIGN KEY(idUtilisateur) REFERENCES Utilisateur(idUtilisateur)
);

CREATE TABLE Question(
    idQuestion SERIAL,
    question TEXT NOT NULL,
    intitule TEXT NOT NULL,
    estValide BOOLEAN NOT NULL DEFAULT FALSE,
    idUtilisateur SERIAL, -- nom de l'organisateur de la question
    CONSTRAINT pk_Question PRIMARY KEY(idQuestion),
    CONSTRAINT fk_organisateur FOREIGN KEY(idUtilisateur) REFERENCES Utilisateur(idUtilisateur)
);