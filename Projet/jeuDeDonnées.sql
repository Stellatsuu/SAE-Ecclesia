-- Utilisateur
INSERT INTO Utilisateur(idUtilisateur, nom, prenom, motDePasse) VALUES(10000, 'Valicov', 'Petru', '1234');
INSERT INTO Utilisateur(idUtilisateur, nom, prenom, motDePasse) VALUES(10001, 'Chollet', 'Antoine', '1234');
INSERT INTO Utilisateur(idUtilisateur, nom, prenom, motDePasse) VALUES(10002, 'Palleja', 'Xavier', '1234');
INSERT INTO Utilisateur(idUtilisateur, nom, prenom, motDePasse) VALUES(10003, 'Palleja', 'Natalie', '1234');
INSERT INTO Utilisateur(idUtilisateur, nom, prenom, motDePasse) VALUES(10004, 'Marie-Jeanne', 'Alain', '1234');
INSERT INTO Utilisateur(idUtilisateur, nom, prenom, motDePasse) VALUES(10005, 'Trombettoni', 'Gilles', '1234');
INSERT INTO Utilisateur(idUtilisateur, nom, prenom, motDePasse) VALUES(10006, 'Poupet', 'Victor', '1234');
INSERT INTO Utilisateur(idUtilisateur, nom, prenom, motDePasse) VALUES(10007, 'Rosenfeld', 'Matthieu', '1234');
INSERT INTO Utilisateur(idUtilisateur, nom, prenom, motDePasse) VALUES(10008, 'Rouchon', 'Bruno', '1234');
INSERT INTO Utilisateur(idUtilisateur, nom, prenom, motDePasse) VALUES(10009, 'Rice', 'Lorraine', '1234');

-- Administrateur

-- Demande_Question
INSERT INTO Demande_Question(titre, intitule, idUtilisateur) VALUES('Est-ce que les sql ninjas existent ?', 'Les sql ninjas une légende urbaine, personne ne les a jamais vus. Pensez-vous qu''ils existent ?', 10002);
INSERT INTO Demande_Question(titre, intitule, idUtilisateur) VALUES('Suis-je prof de maths ?', 'Quelles sont les probabilités que je sois prof de maths ? Fait-il beau ou non ? Cette question me parait fort épineuse..', 10004);

-- Question
INSERT INTO Question(titre, intitule, idUtilisateur, dateDebutRedaction, dateFinRedaction, dateOuvertureVotes, dateFermetureVotes) VALUES('Comment pensez-vous que nous devrions organiser la SAÉ ?', 'Chers collègues, je sais que je m''y prends un petit peu tard, mais je pense que nous devrions commencer à discuter de l''organisation de cette SAÉ...', 100005, '2022-09-05', '2022-09-15', '2022-09-15', '2022-09-20');