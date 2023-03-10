-- CLEAN DES TABLES
DROP TABLE Co_Auteur CASCADE;

DROP TABLE Votant CASCADE;

DROP TABLE Redacteur CASCADE;

DROP TABLE Section CASCADE;

DROP TABLE Question CASCADE;

DROP TABLE Demande_Question CASCADE;

DROP TABLE Administrateur CASCADE;

DROP TABLE Utilisateur CASCADE;

DROP TABLE Proposition CASCADE;

DROP TABLE Paragraphe CASCADE;

DROP TABLE Vote CASCADE;

DROP TABLE Demande_Co_Auteur CASCADE;

-- CREATION DES TABLES
CREATE TABLE Utilisateur (
    username_utilisateur varchar(50) NOT NULL,
    nom_utilisateur varchar(50),
    prenom_utilisateur varchar(50),
    email_utilisateur varchar(100),
    photo_profil bytea,
    mdp_hashed varchar(256) NOT NULL,
    CONSTRAINT pk_Utilisateur PRIMARY KEY (username_utilisateur)
);

CREATE TABLE Administrateur (
    username_administrateur varchar(50) NOT NULL,
    CONSTRAINT pk_Administrateur PRIMARY KEY (username_administrateur),
    CONSTRAINT fk_Administrateur FOREIGN KEY (username_administrateur) REFERENCES Utilisateur (username_utilisateur) ON DELETE CASCADE
);

CREATE TABLE Demande_Question (
    id_demande_question serial,
    titre_demande_question varchar(100) NOT NULL,
    description_demande_question varchar(4000) NOT NULL,
    username_organisateur varchar(50) NOT NULL,
    CONSTRAINT pk_Demande_Question PRIMARY KEY (id_demande_question),
    CONSTRAINT fk_Demande_Question_Utilisateur FOREIGN KEY (username_organisateur) REFERENCES Utilisateur (username_utilisateur) ON DELETE CASCADE
);

CREATE TABLE Question (
    id_question serial,
    titre_question varchar(100) NOT NULL,
    description_question varchar(4000) NOT NULL,
    username_organisateur varchar(50) NOT NULL,
    date_debut_redaction timestamp,
    date_fin_redaction timestamp,
    date_ouverture_votes timestamp,
    date_fermeture_votes timestamp,
    systeme_vote varchar(50),
    tags text[],
    CONSTRAINT pk_question PRIMARY KEY (id_question),
    CONSTRAINT fk_question_utilisateur FOREIGN KEY (username_organisateur) REFERENCES Utilisateur (username_utilisateur) ON DELETE CASCADE
);

CREATE TABLE Section (
    id_section serial,
    id_question serial NOT NULL,
    nom_section varchar(50) NOT NULL,
    description_section varchar(2000) NOT NULL,
    CONSTRAINT pk_section PRIMARY KEY (id_section),
    CONSTRAINT fk_section_question FOREIGN KEY (id_question) REFERENCES Question (id_question) ON DELETE CASCADE
);

CREATE TABLE Redacteur (
    username_redacteur varchar(50),
    id_question serial,
    CONSTRAINT pk_Redacteur PRIMARY KEY (username_redacteur, id_question),
    CONSTRAINT fk_Redacteur FOREIGN KEY (username_redacteur) REFERENCES Utilisateur (username_utilisateur) ON DELETE CASCADE,
    CONSTRAINT fk_Redacteur_Question FOREIGN KEY (id_question) REFERENCES Question (id_question) ON DELETE CASCADE
);

CREATE TABLE Votant (
    username_votant varchar(50),
    id_question serial,
    CONSTRAINT pk_Votant PRIMARY KEY (username_votant, id_question),
    CONSTRAINT fk_Votant FOREIGN KEY (username_votant) REFERENCES Utilisateur (username_utilisateur) ON DELETE CASCADE,
    CONSTRAINT fk_Votant_Question FOREIGN KEY (id_question) REFERENCES Question (id_question) ON DELETE CASCADE
);

CREATE TABLE Proposition (
    id_proposition serial,
    titre_proposition varchar(100) NOT NULL,
    username_responsable varchar(50) NOT NULL,
    id_question serial NOT NULL,
    CONSTRAINT pk_Proposition PRIMARY KEY (id_proposition),
    CONSTRAINT fk_Proposition_Responsable FOREIGN KEY (username_responsable) REFERENCES Utilisateur (username_utilisateur) ON DELETE CASCADE,
    CONSTRAINT fk_Proposition_Question FOREIGN KEY (id_question) REFERENCES Question (id_question) ON DELETE CASCADE,
    CONSTRAINT fk_Proposition_Redacteur_Question FOREIGN KEY (username_responsable, id_question) REFERENCES Redacteur (username_redacteur, id_question) ON DELETE CASCADE
);

CREATE TABLE Paragraphe (
    id_paragraphe serial,
    id_proposition serial NOT NULL,
    id_section serial NOT NULL,
    contenu_paragraphe text NOT NULL,
    CONSTRAINT pk_Paragraphe PRIMARY KEY (id_paragraphe),
    CONSTRAINT fk_Paragraphe_Proposition FOREIGN KEY (id_proposition) REFERENCES Proposition (id_proposition) ON DELETE CASCADE,
    CONSTRAINT fk_Paragraphe_Section FOREIGN KEY (id_section) REFERENCES Section (id_section) ON DELETE CASCADE
);

CREATE TABLE Co_Auteur (
    username_co_auteur varchar(50),
    id_paragraphe serial,
    CONSTRAINT pk_Co_Auteur PRIMARY KEY (username_co_auteur, id_paragraphe),
    CONSTRAINT fk_Co_Auteur FOREIGN KEY (username_co_auteur) REFERENCES Utilisateur (username_utilisateur) ON DELETE CASCADE,
    CONSTRAINT fk_Co_Auteur_Paragraphe FOREIGN KEY (id_paragraphe) REFERENCES Paragraphe (id_paragraphe) ON DELETE CASCADE
);

CREATE TABLE Vote (
    username_votant varchar(50),
    id_proposition serial,
    valeur int,
    CONSTRAINT pk_Vote PRIMARY KEY (username_votant, id_proposition),
    CONSTRAINT fk_Vote_Votant FOREIGN KEY (username_votant) REFERENCES Utilisateur (username_utilisateur) ON DELETE CASCADE,
    CONSTRAINT fk_Vote_Proposition FOREIGN KEY (id_proposition) REFERENCES Proposition (id_proposition) ON DELETE CASCADE
);

CREATE TABLE Demande_Co_Auteur (
    username_demandeur varchar(50),
    id_proposition serial,
    message varchar(1000),
    CONSTRAINT pk_Demande_Co_Auteur PRIMARY KEY (username_demandeur, id_proposition),
    CONSTRAINT fk_Demande_Co_Auteur_Demandeur FOREIGN KEY (username_demandeur) REFERENCES Utilisateur (username_utilisateur) ON DELETE CASCADE,
    CONSTRAINT fk_Demande_Co_Auteur_Proposition FOREIGN KEY (id_proposition) REFERENCES Proposition (id_proposition) ON DELETE CASCADE
);

-- FONCTIONS, PROCEDURES ET TRIGGERS

DROP FUNCTION IF EXISTS utilisateur_est_lie_a_question;
DROP FUNCTION IF EXISTS getPhase;

CREATE OR REPLACE FUNCTION utilisateur_est_lie_a_question (p_username_utilisateur varchar(50), p_id_question integer)
    RETURNS boolean
    LANGUAGE plpgsql
    AS $$
BEGIN
    RETURN EXISTS (
        SELECT
            1
        FROM
            Question q
        WHERE
            q.id_question = p_id_question
            AND q.username_organisateur = p_username_utilisateur)
        OR EXISTS (
            SELECT
                1
            FROM
                Redacteur r
            WHERE
                r.id_question = p_id_question
        AND r.username_redacteur = p_username_utilisateur)
        OR EXISTS (
            SELECT
                1
            FROM
                Votant v
            WHERE
                v.id_question = p_id_question
        AND v.username_votant = p_username_utilisateur)
        OR EXISTS (
            SELECT
                1
            FROM
                Co_Auteur c
                JOIN Paragraphe p ON c.id_paragraphe = p.id_paragraphe
                JOIN Proposition pr ON p.id_proposition = pr.id_proposition
            WHERE
                pr.id_question = p_id_question
                AND c.username_co_auteur = p_username_utilisateur);
END;
$$;

CREATE OR REPLACE FUNCTION getPhase (p_id_question integer)
RETURNS varchar
LANGUAGE plpgsql
AS $$
DECLARE
    v_date_debut_redaction timestamp;
    v_date_fin_redaction timestamp;
    v_date_ouverture_votes timestamp;
    v_date_fermeture_votes timestamp;
BEGIN
    SELECT date_debut_redaction, date_fin_redaction, date_ouverture_votes, date_fermeture_votes
    INTO v_date_debut_redaction, v_date_fin_redaction, v_date_ouverture_votes, v_date_fermeture_votes
    FROM Question
    WHERE id_question = p_id_question;

    IF(v_date_debut_redaction IS NULL OR v_date_fin_redaction IS NULL OR v_date_ouverture_votes IS NULL OR v_date_fermeture_votes IS NULL) THEN
        RETURN 'nonRemplie';
    ELSEIF(CURRENT_TIMESTAMP < v_date_debut_redaction) THEN
        RETURN 'attente';
    ELSEIF(CURRENT_TIMESTAMP >= v_date_debut_redaction AND CURRENT_TIMESTAMP <= v_date_fin_redaction) THEN
        RETURN 'redaction';
    ELSEIF(CURRENT_TIMESTAMP > v_date_fin_redaction AND CURRENT_TIMESTAMP < v_date_ouverture_votes) THEN
        RETURN 'lecture';
    ELSEIF(CURRENT_TIMESTAMP >= v_date_ouverture_votes AND CURRENT_TIMESTAMP <= v_date_fermeture_votes) THEN
        RETURN 'vote';
    ELSEIF(CURRENT_TIMESTAMP > v_date_fermeture_votes) THEN
        RETURN 'resultat';
    ELSE
        RETURN 'erreur';
    END IF;
END;
$$;

CREATE OR REPLACE PROCEDURE supprimer_co_auteurs (p_id_proposition integer)
LANGUAGE plpgsql
AS $$
DECLARE
    v_id_paragraphe integer;
BEGIN
    FOR v_id_paragraphe IN (
        SELECT
            p.id_paragraphe
        FROM
            Paragraphe p
        WHERE
            p.id_proposition = p_id_proposition)
        LOOP
            DELETE FROM Co_Auteur c
            WHERE c.id_paragraphe = v_id_paragraphe;
        END LOOP;
END;
$$;

CREATE OR REPLACE FUNCTION check_question_proposition_section ()
    RETURNS TRIGGER
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF (
        SELECT
            id_question
        FROM
            Proposition
        WHERE
            id_proposition = NEW.id_proposition) != (
    SELECT
        id_question
    FROM
        Section
    WHERE
        id_section = NEW.id_section) THEN
        RAISE EXCEPTION 'La question de la proposition et la question de la section ne sont pas les m??mes';
    END IF;
    RETURN NEW;
END;
$$;

CREATE OR REPLACE FUNCTION check_est_votant ()
    RETURNS TRIGGER
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF (
        SELECT
            username_votant
        FROM
            Votant
        WHERE
            username_votant = NEW.username_votant AND id_question = (
            SELECT
                id_question
            FROM
                Proposition
            WHERE
                id_proposition = NEW.id_proposition)) IS NULL THEN
        RAISE EXCEPTION 'L''utilisateur n''est pas votant pour cette question';
    END IF;
    RETURN NEW;
END;
$$;

CREATE TRIGGER check_question_proposition_section
    BEFORE INSERT OR UPDATE ON Paragraphe
    FOR EACH ROW
    EXECUTE PROCEDURE check_question_proposition_section ();

CREATE TRIGGER check_est_votant
    BEFORE INSERT OR UPDATE ON Vote
    FOR EACH ROW
    EXECUTE PROCEDURE check_est_votant ();

