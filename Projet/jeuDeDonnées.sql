-- Utilisateur
INSERT INTO Utilisateur(id_utilisateur, nom_utilisateur, prenom_utilisateur, mot_de_passe) VALUES(10000, 'Valicov', 'Petru', '1234');
INSERT INTO Utilisateur(id_utilisateur, nom_utilisateur, prenom_utilisateur, mot_de_passe) VALUES(10001, 'Chollet', 'Antoine', '1234');
INSERT INTO Utilisateur(id_utilisateur, nom_utilisateur, prenom_utilisateur, mot_de_passe) VALUES(10002, 'Palleja', 'Xavier', '1234');
INSERT INTO Utilisateur(id_utilisateur, nom_utilisateur, prenom_utilisateur, mot_de_passe) VALUES(10003, 'Palleja', 'Natalie', '1234');
INSERT INTO Utilisateur(id_utilisateur, nom_utilisateur, prenom_utilisateur, mot_de_passe) VALUES(10004, 'Marie-Jeanne', 'Alain', '1234');
INSERT INTO Utilisateur(id_utilisateur, nom_utilisateur, prenom_utilisateur, mot_de_passe) VALUES(10005, 'Trombettoni', 'Gilles', '1234');
INSERT INTO Utilisateur(id_utilisateur, nom_utilisateur, prenom_utilisateur, mot_de_passe) VALUES(10006, 'Poupet', 'Victor', '1234');
INSERT INTO Utilisateur(id_utilisateur, nom_utilisateur, prenom_utilisateur, mot_de_passe) VALUES(10007, 'Rosenfeld', 'Matthieu', '1234');
INSERT INTO Utilisateur(id_utilisateur, nom_utilisateur, prenom_utilisateur, mot_de_passe) VALUES(10008, 'Rouchon', 'Bruno', '1234');
INSERT INTO Utilisateur(id_utilisateur, nom_utilisateur, prenom_utilisateur, mot_de_passe) VALUES(10009, 'Rice', 'Lorraine', '1234');
INSERT INTO Utilisateur(id_utilisateur, nom_utilisateur, prenom_utilisateur, mot_de_passe) VALUES(10010, 'Troll', 'Face', '1234');

-- Administrateur

-- Demande_Question
INSERT INTO Demande_Question(titre_demande_question, description_demande_question, id_organisateur) VALUES('azertyuiop', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 10010);
INSERT INTO Demande_Question(titre_demande_question, description_demande_question, id_organisateur) VALUES('Barèmes des modules de mathématiques', 'Quels devraient être les barèmes pour les différents modules de mathématique cette année?', 10004);
INSERT INTO Demande_Question(titre_demande_question, description_demande_question, id_organisateur) VALUES('Blind Test', 'Quand devrait se dérouler le prochain blind test?', 10001);

-- Question
