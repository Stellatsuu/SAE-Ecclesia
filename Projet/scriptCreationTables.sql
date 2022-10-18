DROP TABLE Questions;
DROP TABLE Administrateurs;
DROP TABLE Utilisateurs;

CREATE TABLE Utilisateurs(
    idUtilisateur SERIAL,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    motDePasse VARCHAR(128) NOT NULL,
    CONSTRAINT pk_Utilisateurs PRIMARY KEY(idUtilisateur)
);

CREATE TABLE Administrateurs(
    idUtilisateur SERIAL,
    CONSTRAINT pk_Administrateurs PRIMARY KEY(idUtilisateur),
    CONSTRAINT fk_Administrateurs FOREIGN KEY(idUtilisateur) REFERENCES Utilisateurs(idUtilisateur)
);

CREATE TABLE Questions(
    idQuestion SERIAL,
    question TEXT NOT NULL,
    intitule TEXT NOT NULL,
    estValide BOOLEAN NOT NULL DEFAULT FALSE,
    idUtilisateur SERIAL, -- nom de l'organisateur de la question
    CONSTRAINT pk_Questions PRIMARY KEY(idQuestion),
    CONSTRAINT fk_organisateur FOREIGN KEY(idUtilisateur) REFERENCES Utilisateurs(idUtilisateur)
);