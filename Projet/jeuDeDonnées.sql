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
INSERT INTO Section(id_section, id_question, nom_section, description_section) VALUES (4001, 10002, 'Section n°1', 'Ceci est la section n°1');
INSERT INTO Section(id_section, id_question, nom_section, description_section) VALUES (4002, 10002, 'Section n°2', 'Ceci est la section n°2');
INSERT INTO Section(id_section, id_question, nom_section, description_section) VALUES (4003, 10002, 'Section n°3', 'Ceci est la section n°3');

-- Responsable/Redacteur
INSERT INTO Redacteur(id_redacteur, id_question) VALUES(10002, 10002); --Palleja X
INSERT INTO Redacteur(id_redacteur, id_question) VALUES(10003, 10002); --Palleja N
INSERT INTO Redacteur(id_redacteur, id_question) VALUES(10004, 10002); --MJ

-- Votant
INSERT INTO Votant(id_votant, id_question) VALUES(10000, 10002); --Valicov
INSERT INTO Votant(id_votant, id_question) VALUES(10001, 10002); --Chollet
INSERT INTO Votant(id_votant, id_question) VALUES(10002, 10002); --Palleja X
INSERT INTO Votant(id_votant, id_question) VALUES(10003, 10002); --Palleja N
INSERT INTO Votant(id_votant, id_question) VALUES(10004, 10002); --MJ

-- Proposition
INSERT INTO Proposition(id_proposition, titre_proposition, id_redacteur, id_question) VALUES (20001, 'Proposition n°1', 10002, 10002); --Palleja X
INSERT INTO Proposition(id_proposition, titre_proposition, id_redacteur, id_question) VALUES (20002, 'Proposition n°2', 10003, 10002); --Palleja N
INSERT INTO Proposition(id_proposition, titre_proposition, id_redacteur, id_question) VALUES (20003, 'Proposition n°3', 10004, 10002); --MJ

-- Paragraphe
INSERT INTO Paragraphe(id_paragraphe, id_proposition, id_section, contenu_paragraphe) VALUES (3001, 20001, 4001, 'Ceci est le paragraphe n°1 de la proposition n°1');
INSERT INTO Paragraphe(id_paragraphe, id_proposition, id_section, contenu_paragraphe) VALUES (3002, 20001, 4002, 'Ceci est le paragraphe n°2 de la proposition n°1');
INSERT INTO Paragraphe(id_paragraphe, id_proposition, id_section, contenu_paragraphe) VALUES (3003, 20001, 4003, 'Ceci est le paragraphe n°3 de la proposition n°1');
INSERT INTO Paragraphe(id_paragraphe, id_proposition, id_section, contenu_paragraphe) VALUES (3004, 20002, 4001, 'Ceci est le paragraphe n°1 de la proposition n°2');
INSERT INTO Paragraphe(id_paragraphe, id_proposition, id_section, contenu_paragraphe) VALUES (3005, 20002, 4002, 'Ceci est le paragraphe n°2 de la proposition n°2');
INSERT INTO Paragraphe(id_paragraphe, id_proposition, id_section, contenu_paragraphe) VALUES (3006, 20002, 4003, 'Ceci est le paragraphe n°3 de la proposition n°2');
INSERT INTO Paragraphe(id_paragraphe, id_proposition, id_section, contenu_paragraphe) VALUES (3007, 20003, 4001, 'Ceci est le paragraphe n°1 de la proposition n°3');
INSERT INTO Paragraphe(id_paragraphe, id_proposition, id_section, contenu_paragraphe) VALUES (3008, 20003, 4002, 'Ceci est le paragraphe n°2 de la proposition n°3');
INSERT INTO Paragraphe(id_paragraphe, id_proposition, id_section, contenu_paragraphe) VALUES (3009, 20003, 4003, 'Ceci est le paragraphe n°3 de la proposition n°3');

-- Co_Auteur
INSERT INTO Co_Auteur(id_utilisateur, id_paragraphe) VALUES (10008,3001); --Rouchon
INSERT INTO Co_Auteur(id_utilisateur, id_paragraphe) VALUES (10000, 3001); --Petru

-- Vote
INSERT INTO Vote(id_votant, id_proposition, valeur) VALUES (10000, 20001, 1); --Valicov
INSERT INTO Vote(id_votant, id_proposition, valeur) VALUES (10001, 20002, 1); --Chollet
INSERT INTO Vote(id_votant, id_proposition, valeur) VALUES (10002, 20003, 1); --Palleja X
INSERT INTO Vote(id_votant, id_proposition, valeur) VALUES (10003, 20003, 1); --Palleja N
INSERT INTO Vote(id_votant, id_proposition, valeur) VALUES (10004, 20003, 1); --MJ