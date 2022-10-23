-- Utilisateurs
INSERT INTO Utilisateur(idUtilisateur, nom, prenom, motDePasse) VALUES(1, 'Uzumaki', 'Naruto', '1234');
INSERT INTO Utilisateur(nom, prenom, motDePasse) VALUES('Uchiha', 'Sasuke', '1234');
INSERT INTO Utilisateur(nom, prenom, motDePasse) VALUES('Hatake', 'Kakashi', '1234');
INSERT INTO Utilisateur(nom, prenom, motDePasse) VALUES('Namizake', 'Minato', '1234');
INSERT INTO Utilisateur(nom, prenom, motDePasse) VALUES('Uchiha', 'Itachi', '1234');

-- Administrateurs

-- Questions
INSERT INTO Demande_Question(titre, intitule, idUtilisateur) VALUES('Doit-on attaquer l''akatsuki ?', 'L''akatsuki est de plus en plus menaçante, nous avons répéré leur repaire, devrions-nous lancer un assaut en association avec le village caché du sable, Suna ? Et sous quelle forme ?', 1);
INSERT INTO Demande_Question(titre, intitule, idUtilisateur, estValide) VALUES('Comment devrions-nous organiser notre sortie pour manger des ramens chez Ichiraku ?', 'Ichiraku a présenté de nouvelles recettes de ramens, discutons de notre organisation.', 1, TRUE);