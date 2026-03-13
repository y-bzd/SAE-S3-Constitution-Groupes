DROP TABLE IF EXISTS enseigne;
DROP TABLE IF EXISTS respecte;
DROP TABLE IF EXISTS Contient;
DROP TABLE IF EXISTS ReponseSondage;
DROP TABLE IF EXISTS OptionSondage;
DROP TABLE IF EXISTS Sondage;
DROP TABLE IF EXISTS Affectation;
DROP TABLE IF EXISTS Responsable;
DROP TABLE IF EXISTS Contrainte;
DROP TABLE IF EXISTS Note;
DROP TABLE IF EXISTS FeuilleNote;
DROP TABLE IF EXISTS ChoixCollegue;
DROP TABLE IF EXISTS Etudiant;
DROP TABLE IF EXISTS Groupe;
DROP TABLE IF EXISTS Promotion;
DROP TABLE IF EXISTS Formation;
DROP TABLE IF EXISTS Enseignant;
DROP TABLE IF EXISTS Utilisateur;

CREATE TABLE Utilisateur(
   id_utilisateur INT,
   identifiant_connexion VARCHAR(255) NOT NULL,
   hash_mdp VARCHAR(255) NOT NULL,
   code_role VARCHAR(64) NOT NULL,
   PRIMARY KEY(id_utilisateur)
);

CREATE TABLE Enseignant(
   id_enseignant INT,
   nom_enseignant VARCHAR(255) NOT NULL,
   prenom_enseignant VARCHAR(255) NOT NULL,
   id_utilisateur INT NOT NULL,
   PRIMARY KEY(id_enseignant),
   UNIQUE(id_utilisateur),
   FOREIGN KEY(id_utilisateur) REFERENCES Utilisateur(id_utilisateur)
);

CREATE TABLE Formation(
   id_formation INT,
   libelle_formation VARCHAR(255) NOT NULL,
   PRIMARY KEY(id_formation)
);

CREATE TABLE Promotion(
   id_promotion INT,
   libelle_promotion VARCHAR(255) NOT NULL,
   id_formation INT,
   PRIMARY KEY(id_promotion),
   FOREIGN KEY(id_formation) REFERENCES Formation(id_formation)
);

CREATE TABLE Groupe(
   id_groupe INT,
   libelle_groupe VARCHAR(255),
   type_groupe VARCHAR(64),
   capacite_max_groupe INT,
   groupe_rendu_public BOOLEAN,
   PRIMARY KEY(id_groupe),
   UNIQUE(libelle_groupe)
);

CREATE TABLE Etudiant(
   id_etudiant INT,
   numero_etudiant VARCHAR(64),
   nom_etudiant VARCHAR(255),
   prenom_etudiant VARCHAR(255),
   genre_etudiant VARCHAR(32),
   email_etudiant VARCHAR(255),
   tel_etudiant VARCHAR(64),
   rue_etudiant VARCHAR(255),
   ville_etudiant VARCHAR(128),
   code_postal_etudiant VARCHAR(16),
   type_bac VARCHAR(64),
   periode_redoublement VARCHAR(64),
   parcours_etudiant VARCHAR(255),
   PRIMARY KEY(id_etudiant),
   UNIQUE(numero_etudiant)
);

CREATE TABLE ChoixCollegue (
    id_etudiant INT NOT NULL,
    id_collegue INT NOT NULL,
    PRIMARY KEY (id_etudiant, id_collegue),
    FOREIGN KEY (id_etudiant) REFERENCES Etudiant(id_etudiant),
    FOREIGN KEY (id_collegue) REFERENCES Etudiant(id_etudiant)
);

CREATE TABLE FeuilleNote(
   id_importation_notes INT,
   date_importation DATE,
   PRIMARY KEY(id_importation_notes),
   UNIQUE(date_importation)
);

CREATE TABLE Note(
   id_note INT,
   libelle_note VARCHAR(255),
   valeur_note DECIMAL(8,2),
   id_etudiant INT,
   id_importation_notes INT,
   PRIMARY KEY(id_note),
   UNIQUE(libelle_note),
   FOREIGN KEY(id_etudiant) REFERENCES Etudiant(id_etudiant),
   FOREIGN KEY(id_importation_notes) REFERENCES FeuilleNote(id_importation_notes)
);

CREATE TABLE Contrainte(
   id_contrainte INT,
   type_contrainte VARCHAR(128),
   parametres_contrainte VARCHAR(1024),
   PRIMARY KEY(id_contrainte)
);

CREATE TABLE Responsable(
   id_responsable INT,
   portee_responsable VARCHAR(128) NOT NULL,
   droits_responsable VARCHAR(255) NOT NULL,
   id_formation INT NOT NULL,
   id_enseignant INT NOT NULL,
   PRIMARY KEY(id_responsable),
   UNIQUE(id_enseignant),
   FOREIGN KEY(id_formation) REFERENCES Formation(id_formation),
   FOREIGN KEY(id_enseignant) REFERENCES Enseignant(id_enseignant)
);

CREATE TABLE Affectation(
   id_affectation INT AUTO_INCREMENT,
   date_affectation DATETIME,
   affectation_courante BOOLEAN,
   id_groupe INT,
   id_etudiant INT,
   PRIMARY KEY(id_affectation),
   FOREIGN KEY(id_groupe) REFERENCES Groupe(id_groupe),
   FOREIGN KEY(id_etudiant) REFERENCES Etudiant(id_etudiant)
);

CREATE TABLE Sondage(
   id_sondage INT,
   critere_sondage VARCHAR(255),
   type_sondage VARCHAR(64),
   id_responsable INT NOT NULL,
   PRIMARY KEY(id_sondage),
   FOREIGN KEY(id_responsable) REFERENCES Responsable(id_responsable)
);

CREATE TABLE OptionSondage(
   id_option INT,
   texte_option VARCHAR(255),
   id_sondage INT,
   PRIMARY KEY(id_option),
   FOREIGN KEY(id_sondage) REFERENCES Sondage(id_sondage)
);

CREATE TABLE ReponseSondage(
   id_reponse INT,
   valeur_reponse VARCHAR(255),
   id_etudiant INT,
   id_sondage INT,
   PRIMARY KEY(id_reponse),
   FOREIGN KEY(id_etudiant) REFERENCES Etudiant(id_etudiant),
   FOREIGN KEY(id_sondage) REFERENCES Sondage(id_sondage)
);

CREATE TABLE Contient(
   id_promotion INT,
   id_groupe INT,
   nb_groupes VARCHAR(50),
   PRIMARY KEY(id_promotion, id_groupe),
   FOREIGN KEY(id_promotion) REFERENCES Promotion(id_promotion),
   FOREIGN KEY(id_groupe) REFERENCES Groupe(id_groupe)
);

CREATE TABLE respecte(
   id_contrainte INT,
   id_groupe INT,
   priorite_contrainte VARCHAR(50),
   PRIMARY KEY(id_contrainte, id_groupe),
   FOREIGN KEY(id_contrainte) REFERENCES Contrainte(id_contrainte),
   FOREIGN KEY(id_groupe) REFERENCES Groupe(id_groupe)
);

CREATE TABLE enseigne(
   id_enseignant INT,
   id_etudiant INT,
   PRIMARY KEY(id_enseignant, id_etudiant),
   FOREIGN KEY(id_enseignant) REFERENCES Enseignant(id_enseignant),
   FOREIGN KEY(id_etudiant) REFERENCES Etudiant(id_etudiant)
);