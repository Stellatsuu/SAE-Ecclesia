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
INSERT INTO Administrateur(id_administrateur) VALUES(10005); --Trombettoni

-- Demande_Question
INSERT INTO Demande_Question(titre_demande_question, description_demande_question, id_organisateur) VALUES('azertyuiop', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 10010); --Troll
INSERT INTO Demande_Question(titre_demande_question, description_demande_question, id_organisateur) VALUES('Barèmes des modules de mathématiques', 'Quels devraient être les barèmes pour les différents modules de mathématique cette année?', 10004); --MJ
INSERT INTO Demande_Question(titre_demande_question, description_demande_question, id_organisateur) VALUES('Blind Test', 'Quand devrait se dérouler le prochain blind test?', 10001); --Chollet

-- Question
INSERT INTO Question(id_question, titre_question, description_question, id_organisateur) VALUES(10001, 'Cryptographie', 'Est-ce que le chiffrement symétrique est un bon chiffrement ?', 10004); --MJ
INSERT INTO Question(id_question, titre_question, description_question, id_organisateur) VALUES(10002, 'Mythes SQL', 'Les SQL Ninjas : Mythologie ou réalité ?', 10002); --Palleja X

-- Section

-- Responsable/Redacteur
INSERT INTO Redacteur(id_redacteur, id_question) VALUES(10002, 10002); --Palleja X
INSERT INTO Redacteur(id_redacteur, id_question) VALUES(10003, 10002); --Palleja N

-- Votant
INSERT INTO Votant(id_votant, id_question) VALUES(10008, 10002); --Rouchon
INSERT INTO Votant(id_votant, id_question) VALUES(10004, 10002); --MJ

-- Proposition
INSERT INTO Proposition(id_proposition, titre_proposition, id_redacteur, id_question) VALUES (20001, 'test proposition', 10003, 10002) --Palleja N

-- Paragraphe

-- Co_Auteur


