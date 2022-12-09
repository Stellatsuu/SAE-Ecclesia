-- Utilisateur
INSERT INTO Utilisateur(username_utilisateur, nom_utilisateur, prenom_utilisateur, email_utilisateur, photo_profil, mdp_hashed) VALUES('petruv', 'Valicov', 'Petru', NULL, NULL, '1234');
INSERT INTO Utilisateur(username_utilisateur, nom_utilisateur, prenom_utilisateur, email_utilisateur, photo_profil, mdp_hashed) VALUES('chatoine', 'Chollet', 'Antoine', NULL, NULL, '1234');
INSERT INTO Utilisateur(username_utilisateur, nom_utilisateur, prenom_utilisateur, email_utilisateur, photo_profil, mdp_hashed) VALUES('xavierp', 'Palleja', 'Xavier', NULL, NULL, '1234');
INSERT INTO Utilisateur(username_utilisateur, nom_utilisateur, prenom_utilisateur, email_utilisateur, photo_profil, mdp_hashed) VALUES('nathaliep', 'Palleja', 'Nathalie', NULL, NULL, '1234');
INSERT INTO Utilisateur(username_utilisateur, nom_utilisateur, prenom_utilisateur, email_utilisateur, photo_profil, mdp_hashed) VALUES('alainmj', 'Marie-Jeanne', 'Alain', NULL, NULL, '1234');
INSERT INTO Utilisateur(username_utilisateur, nom_utilisateur, prenom_utilisateur, email_utilisateur, photo_profil, mdp_hashed) VALUES('gillest', 'Trombettoni', 'Gilles', NULL, NULL, '1234');
INSERT INTO Utilisateur(username_utilisateur, nom_utilisateur, prenom_utilisateur, email_utilisateur, photo_profil, mdp_hashed) VALUES('poupetv', 'Poupet', 'Victor', NULL, NULL, '1234');
INSERT INTO Utilisateur(username_utilisateur, nom_utilisateur, prenom_utilisateur, email_utilisateur, photo_profil, mdp_hashed) VALUES('matthieur', 'Rosenfeld', 'Matthieu', NULL, NULL, '1234');
INSERT INTO Utilisateur(username_utilisateur, nom_utilisateur, prenom_utilisateur, email_utilisateur, photo_profil, mdp_hashed) VALUES('brunor', 'Rouchon', 'Bruno', NULL, NULL, '1234');
INSERT INTO Utilisateur(username_utilisateur, nom_utilisateur, prenom_utilisateur, email_utilisateur, photo_profil, mdp_hashed) VALUES('lorrainer', 'Rice', 'Lorraine', NULL, NULL, '1234');
INSERT INTO Utilisateur(username_utilisateur, nom_utilisateur, prenom_utilisateur, email_utilisateur, photo_profil, mdp_hashed) VALUES('trollface', 'Troll', 'Face', NULL, NULL, '1234');
INSERT INTO Utilisateur(username_utilisateur, nom_utilisateur, prenom_utilisateur, email_utilisateur, photo_profil, mdp_hashed) VALUES('test', 'Test', 'Test', NULL, NULL, '1234');

-- Administrateur
INSERT INTO Administrateur(username_administrateur) VALUES('gillest'); --Trombettoni

-- Demande_Question
INSERT INTO Demande_Question(titre_demande_question, description_demande_question, username_organisateur) VALUES('azertyuiop', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 'trollface'); --Troll
INSERT INTO Demande_Question(titre_demande_question, description_demande_question, username_organisateur) VALUES('Barèmes des modules de mathématiques', 'Quels devraient être les barèmes pour les différents modules de mathématique cette année?', 'alainmj'); --Marie-Jeanne
INSERT INTO Demande_Question(titre_demande_question, description_demande_question, username_organisateur) VALUES('Blind Test', 'Quand devrait se dérouler le prochain blind test?', 'chatoine'); --Chollet

-- Question
INSERT INTO Question(id_question, titre_question, description_question, username_organisateur) VALUES(10001, 'Cryptographie', 'Est-ce que le chiffrement symétrique est un bon chiffrement ?', 'alainmj'); --Marie-Jeanne
INSERT INTO Question(id_question, titre_question, description_question, username_organisateur, date_debut_redaction, date_fin_redaction, date_ouverture_votes, date_fermeture_votes) VALUES(10002, 'Mythes SQL', 'Les SQL Ninjas : Mythologie ou réalité ?', 'xavierp', '2020-01-01 00:00:00', '2024-01-01 00:00:00', '2024-01-01 00:00:00', '2024-01-01 00:00:00'); --X Palleja

-- Section
INSERT INTO Section(id_section, id_question, nom_section, description_section) VALUES (4001, 10002, 'Section n°1', 'Ceci est la section n°1');
INSERT INTO Section(id_section, id_question, nom_section, description_section) VALUES (4002, 10002, 'Section n°2', 'Ceci est la section n°2');
INSERT INTO Section(id_section, id_question, nom_section, description_section) VALUES (4003, 10002, 'Section n°3', 'Ceci est la section n°3');

-- Redacteur
INSERT INTO Redacteur(username_redacteur, id_question) VALUES('xavierp', 10002); --Palleja X
INSERT INTO Redacteur(username_redacteur, id_question) VALUES('nathaliep', 10002); --Palleja N
INSERT INTO Redacteur(username_redacteur, id_question) VALUES('alainmj', 10002); --MJ

-- Votant
INSERT INTO Votant(username_votant, id_question) VALUES('petruv', 10002); --Valicov
INSERT INTO Votant(username_votant, id_question) VALUES('chatoine', 10002); --Chollet
INSERT INTO Votant(username_votant, id_question) VALUES('xavierp', 10002); --Palleja X
INSERT INTO Votant(username_votant, id_question) VALUES('nathaliep', 10002); --Palleja N
INSERT INTO Votant(username_votant, id_question) VALUES('alainmj', 10002); --MJ

-- Proposition
INSERT INTO Proposition(id_proposition, titre_proposition, username_responsable, id_question) VALUES (20001, 'Proposition n°1', 'xavierp', 10002); --Palleja X
INSERT INTO Proposition(id_proposition, titre_proposition, username_responsable, id_question) VALUES (20002, 'Proposition n°2', 'nathaliep', 10002); --Palleja N
INSERT INTO Proposition(id_proposition, titre_proposition, username_responsable, id_question) VALUES (20003, 'Proposition n°3', 'alainmj', 10002); --MJ

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
INSERT INTO Co_Auteur(username_co_auteur, id_paragraphe) VALUES ('petruv', 3007); --Valicov pour la prop de MJ
INSERT INTO Co_Auteur(username_co_auteur, id_paragraphe) VALUES ('petruv', 3008); --Valicov pour la prop de MJ
INSERT INTO Co_Auteur(username_co_auteur, id_paragraphe) VALUES ('petruv', 3009); --Valicov pour la prop de MJ

-- Vote
INSERT INTO Vote(username_votant, id_proposition, valeur) VALUES ('petruv', 20001, 1); --Valicov
INSERT INTO Vote(username_votant, id_proposition, valeur) VALUES ('chatoine', 20002, 1); --Chollet
INSERT INTO Vote(username_votant, id_proposition, valeur) VALUES ('xavierp', 20003, 1); --Palleja X
INSERT INTO Vote(username_votant, id_proposition, valeur) VALUES ('nathaliep', 20003, 1); --Palleja N
