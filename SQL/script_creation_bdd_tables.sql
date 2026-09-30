-- ============================================
-- Script de création de Base de Données
-- Système de Gestion Scolaire
-- MySQL 8.0+
-- ============================================

-- Création de la base de données
DROP DATABASE IF EXISTS erpschool_bdd_v1;

CREATE DATABASE erpschool_bdd_v1
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE erpschool_bdd_v1;

-- ============================================
-- Tables de référence
-- ============================================

-- Table ANNEES_SCOLAIRES
CREATE TABLE ANNEES_SCOLAIRES (
    id_annee_scolaire INT PRIMARY KEY AUTO_INCREMENT,
    libelle VARCHAR(50) NOT NULL UNIQUE,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    en_cours TINYINT(1) DEFAULT 0,
    CONSTRAINT CHK_dates_annee CHECK (date_fin > date_debut)
) ENGINE=InnoDB;

-- Table TYPES_PAIEMENT
CREATE TABLE TYPES_PAIEMENT (
    id_type INT PRIMARY KEY AUTO_INCREMENT,
    code_type VARCHAR(20) NOT NULL UNIQUE,
    libelle VARCHAR(100) NOT NULL,
    description VARCHAR(500),
    actif TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

-- Table UTILISATEURS
CREATE TABLE UTILISATEURS (
    id_utilisateur INT PRIMARY KEY AUTO_INCREMENT,
    login VARCHAR(50) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    role ENUM('Admin', 'Caissier', 'Professeur', 'Secrétaire') NOT NULL,
    email VARCHAR(100),
    actif TINYINT(1) DEFAULT 1,
    derniere_connexion DATETIME
) ENGINE=InnoDB;

-- ============================================
-- Tables principales
-- ============================================

-- Table ETUDIANTS
CREATE TABLE ETUDIANTS (
    id_etudiant INT PRIMARY KEY AUTO_INCREMENT,
    matricule VARCHAR(50) NOT NULL UNIQUE,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    date_naissance DATE NOT NULL,
    sexe CHAR(1) CHECK (sexe IN ('M', 'F')),
    telephone VARCHAR(20),
    email VARCHAR(100),
    adresse VARCHAR(500),
    nom_parent VARCHAR(200),
    telephone_parent VARCHAR(20),
    photo VARCHAR(500),
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    actif TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

-- Table CLASSES
CREATE TABLE CLASSES (
    id_classe INT PRIMARY KEY AUTO_INCREMENT,
    nom_classe VARCHAR(100) NOT NULL,
    niveau VARCHAR(50) NOT NULL,
    capacite_max INT,
    frais_inscription DECIMAL(18,2),
    frais_scolarite DECIMAL(18,2),
    id_annee_scolaire INT NOT NULL,
    active TINYINT(1) DEFAULT 1,
    CONSTRAINT FK_classes_annee FOREIGN KEY (id_annee_scolaire)
        REFERENCES ANNEES_SCOLAIRES(id_annee_scolaire)
) ENGINE=InnoDB;

-- Table MATIERES
CREATE TABLE MATIERES (
    id_matiere INT PRIMARY KEY AUTO_INCREMENT,
    code_matiere VARCHAR(20) NOT NULL UNIQUE,
    nom_matiere VARCHAR(100) NOT NULL,
    description VARCHAR(500),
    coefficient INT DEFAULT 1,
    active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

-- Table PROFESSEURS
CREATE TABLE PROFESSEURS (
    id_professeur INT PRIMARY KEY AUTO_INCREMENT,
    matricule VARCHAR(50) NOT NULL UNIQUE,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20),
    email VARCHAR(100),
    specialite VARCHAR(100),
    date_embauche DATE,
    salaire DECIMAL(18,2),
    actif TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

-- Table INSCRIPTIONS
CREATE TABLE INSCRIPTIONS (
    id_inscription INT PRIMARY KEY AUTO_INCREMENT,
    id_etudiant INT NOT NULL,
    id_classe INT NOT NULL,
    id_annee_scolaire INT NOT NULL,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    type_inscription ENUM('Inscription', 'Réinscription'),
    statut ENUM('En cours', 'Validée', 'Annulée', 'Terminée'),
    montant_total DECIMAL(18,2),
    montant_paye DECIMAL(18,2) DEFAULT 0,
    solde DECIMAL(18,2) GENERATED ALWAYS AS (montant_total - montant_paye) STORED,
    remarques TEXT,
    CONSTRAINT FK_inscriptions_etudiant FOREIGN KEY (id_etudiant)
        REFERENCES ETUDIANTS(id_etudiant),
    CONSTRAINT FK_inscriptions_classe FOREIGN KEY (id_classe)
        REFERENCES CLASSES(id_classe),
    CONSTRAINT FK_inscriptions_annee FOREIGN KEY (id_annee_scolaire)
        REFERENCES ANNEES_SCOLAIRES(id_annee_scolaire)
) ENGINE=InnoDB;

-- Table ENSEIGNEMENTS
CREATE TABLE ENSEIGNEMENTS (
    id_enseignement INT PRIMARY KEY AUTO_INCREMENT,
    id_classe INT NOT NULL,
    id_matiere INT NOT NULL,
    id_professeur INT NOT NULL,
    heures_semaine INT,
    jour_semaine VARCHAR(20),
    heure_debut TIME,
    heure_fin TIME,
    salle VARCHAR(50),
    CONSTRAINT FK_enseignements_classe FOREIGN KEY (id_classe)
        REFERENCES CLASSES(id_classe),
    CONSTRAINT FK_enseignements_matiere FOREIGN KEY (id_matiere)
        REFERENCES MATIERES(id_matiere),
    CONSTRAINT FK_enseignements_professeur FOREIGN KEY (id_professeur)
        REFERENCES PROFESSEURS(id_professeur)
) ENGINE=InnoDB;

-- Table PAIEMENTS
CREATE TABLE PAIEMENTS (
    id_paiement INT PRIMARY KEY AUTO_INCREMENT,
    id_etudiant INT NOT NULL,
    id_inscription INT,
    id_type_paiement INT NOT NULL,
    montant DECIMAL(18,2) NOT NULL,
    date_paiement DATETIME DEFAULT CURRENT_TIMESTAMP,
    mode_paiement ENUM('Espèces', 'Chèque', 'Virement', 'Mobile Money'),
    numero_recu VARCHAR(50) NOT NULL UNIQUE,
    reference VARCHAR(100),
    id_utilisateur_caisse INT,
    remarques TEXT,
    CONSTRAINT FK_paiements_etudiant FOREIGN KEY (id_etudiant)
        REFERENCES ETUDIANTS(id_etudiant),
    CONSTRAINT FK_paiements_inscription FOREIGN KEY (id_inscription)
        REFERENCES INSCRIPTIONS(id_inscription),
    CONSTRAINT FK_paiements_type FOREIGN KEY (id_type_paiement)
        REFERENCES TYPES_PAIEMENT(id_type),
    CONSTRAINT FK_paiements_utilisateur FOREIGN KEY (id_utilisateur_caisse)
        REFERENCES UTILISATEURS(id_utilisateur)
) ENGINE=InnoDB;

-- Table PERIODES_EVALUATION
CREATE TABLE PERIODES_EVALUATION (
    id_periode INT PRIMARY KEY AUTO_INCREMENT,
    id_annee_scolaire INT NOT NULL,
    libelle VARCHAR(100) NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    ordre INT,
    active TINYINT(1) DEFAULT 1,
    CONSTRAINT FK_periodes_annee FOREIGN KEY (id_annee_scolaire)
        REFERENCES ANNEES_SCOLAIRES(id_annee_scolaire),
    CONSTRAINT CHK_dates_periode CHECK (date_fin > date_debut)
) ENGINE=InnoDB;

-- Table NOTES
CREATE TABLE NOTES (
    id_note INT PRIMARY KEY AUTO_INCREMENT,
    id_etudiant INT NOT NULL,
    id_matiere INT NOT NULL,
    id_professeur INT NOT NULL,
    id_periode INT NOT NULL,
    type_evaluation ENUM('Devoir', 'Interrogation', 'Examen', 'Composition'),
    note DECIMAL(5,2) NOT NULL,
    note_sur DECIMAL(5,2) DEFAULT 20,
    date_evaluation DATE DEFAULT (CURRENT_DATE),
    observations TEXT,
    CONSTRAINT FK_notes_etudiant FOREIGN KEY (id_etudiant)
        REFERENCES ETUDIANTS(id_etudiant),
    CONSTRAINT FK_notes_matiere FOREIGN KEY (id_matiere)
        REFERENCES MATIERES(id_matiere),
    CONSTRAINT FK_notes_professeur FOREIGN KEY (id_professeur)
        REFERENCES PROFESSEURS(id_professeur),
    CONSTRAINT FK_notes_periode FOREIGN KEY (id_periode)
        REFERENCES PERIODES_EVALUATION(id_periode),
    CONSTRAINT CHK_note_valide CHECK (note >= 0 AND note <= note_sur)
) ENGINE=InnoDB;

-- Table ABSENCES
CREATE TABLE ABSENCES (
    id_absence INT PRIMARY KEY AUTO_INCREMENT,
    id_etudiant INT NOT NULL,
    id_enseignement INT NOT NULL,
    id_professeur INT NOT NULL,
    date_absence DATE NOT NULL,
    heure_debut TIME,
    heure_fin TIME,
    type_absence ENUM('Absence', 'Retard'),
    justifiee TINYINT(1) DEFAULT 0,
    motif VARCHAR(500),
    remarques TEXT,
    CONSTRAINT FK_absences_etudiant FOREIGN KEY (id_etudiant)
        REFERENCES ETUDIANTS(id_etudiant),
    CONSTRAINT FK_absences_enseignement FOREIGN KEY (id_enseignement)
        REFERENCES ENSEIGNEMENTS(id_enseignement),
    CONSTRAINT FK_absences_professeur FOREIGN KEY (id_professeur)
        REFERENCES PROFESSEURS(id_professeur)
) ENGINE=InnoDB;

-- ============================================
-- Index pour optimiser les performances
-- ============================================

CREATE INDEX IDX_etudiants_nom ON ETUDIANTS(nom, prenom);
CREATE INDEX IDX_etudiants_actif ON ETUDIANTS(actif);
CREATE INDEX IDX_inscriptions_etudiant ON INSCRIPTIONS(id_etudiant);
CREATE INDEX IDX_inscriptions_statut ON INSCRIPTIONS(statut);
CREATE INDEX IDX_paiements_etudiant ON PAIEMENTS(id_etudiant);
CREATE INDEX IDX_paiements_date ON PAIEMENTS(date_paiement);
CREATE INDEX IDX_notes_etudiant ON NOTES(id_etudiant);
CREATE INDEX IDX_notes_periode ON NOTES(id_periode);
CREATE INDEX IDX_absences_etudiant ON ABSENCES(id_etudiant);
CREATE INDEX IDX_absences_date ON ABSENCES(date_absence);

-- ============================================
-- Données de base (exemples)
-- ============================================

-- Types de paiement standards
INSERT INTO TYPES_PAIEMENT (code_type, libelle, description) VALUES
('INSC', 'Frais d''inscription', 'Frais payés lors de la première inscription'),
('SCOL', 'Frais de scolarité', 'Frais de scolarité mensuels ou annuels'),
('REINSC', 'Frais de réinscription', 'Frais de réinscription pour les années suivantes'),
('EXAM', 'Frais d''examen', 'Frais liés aux examens et compositions'),
('TENUE', 'Frais de tenue', 'Achat de l''uniforme scolaire'),
('LIVRE', 'Frais de livres', 'Achat de manuels et fournitures'),
('CANTINE', 'Frais de cantine', 'Frais de restauration scolaire'),
('TRANSPORT', 'Frais de transport', 'Frais de transport scolaire');

-- Utilisateur administrateur par défaut (mot de passe: Admin123)
-- ⚠️ ATTENTION : en production, stockez un hash (bcrypt/argon2), pas le mot de passe en clair
INSERT INTO UTILISATEURS (login, mot_de_passe, nom, prenom, role, email) VALUES
('admin', 'Admin123', 'Administrateur', 'Système', 'Admin', 'admin@ecole.cg');

SELECT 'Base de données créée avec succès !' AS Message;
SELECT 'Utilisateur par défaut: admin / Admin123' AS Info;
SELECT 'IMPORTANT: Changez le mot de passe administrateur !' AS Avertissement;