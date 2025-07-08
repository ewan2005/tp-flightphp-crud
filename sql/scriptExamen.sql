DROP DATABASE IF EXISTS etablissement;
CREATE DATABASE etablissement;
use etablissement;
-- 1. TABLE ETABLISSEMENT FINANCIER

CREATE TABLE ef_etablissement_financier (
    id_etablissement INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    solde DECIMAL(15,2) DEFAULT 0.0
);

-- 2. TABLE STATUT (aucune dépendance)
CREATE TABLE ef_statut (
    id_statut INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
);

-- 3. TABLE UTILISATEUR (admin & agent)
CREATE TABLE ef_utilisateur (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin', 'agent') NOT NULL,
    id_etablissement INT,
    FOREIGN KEY (id_etablissement) REFERENCES ef_etablissement_financier(id_etablissement)
);

-- 4. TABLE CLIENT (aucune dépendance)
CREATE TABLE ef_client (
    id_client INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    date_naissance DATE NOT NULL,
    email VARCHAR(100) UNIQUE,
    telephone VARCHAR(20)
);

-- 5. TABLE TYPE DE PRET
CREATE TABLE ef_type_pret (
    id_type_pret INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE,
    taux_annuel DECIMAL(5,2) NOT NULL,
    duree_max INT NOT NULL,
    montant_min DECIMAL(15,2) NOT NULL,
    montant_max DECIMAL(15,2) NOT NULL,
    id_etablissement INT NOT NULL,
    FOREIGN KEY (id_etablissement) REFERENCES ef_etablissement_financier(id_etablissement)
);

-- 6. TABLE PRET
CREATE TABLE ef_pret (
    id_pret INT AUTO_INCREMENT PRIMARY KEY,
    id_client INT NOT NULL,
    id_type_pret INT NOT NULL,
    montant DECIMAL(15,2) NOT NULL,
    duree INT NOT NULL,
    date_demande DATE NOT NULL,
    id_statut INT NOT NULL,
    id_agent INT NOT NULL,
    assurance INT NOT NULL DEFAULT 0, -- 0 pour non, 1 pour oui
    FOREIGN KEY (id_client) REFERENCES ef_client(id_client),
    FOREIGN KEY (id_type_pret) REFERENCES ef_type_pret(id_type_pret),
    FOREIGN KEY (id_statut) REFERENCES ef_statut(id_statut),
    FOREIGN KEY (id_agent) REFERENCES ef_utilisateur(id_utilisateur)
);

--reboursement
CREATE TABLE ef_echeance_pret (
    id_echeance INT PRIMARY KEY AUTO_INCREMENT,
    id_pret INT NOT NULL,
    mois_numero INT NOT NULL,                     
    date_echeance DATE NOT NULL,                  
    montant_annuite DECIMAL(12,2) NOT NULL,       
    part_interet DECIMAL(12,2) NOT NULL,          
    part_capital DECIMAL(12,2) NOT NULL,          
    reste_a_payer DECIMAL(12,2) NOT NULL,         
    est_paye BOOLEAN DEFAULT FALSE,               
    FOREIGN KEY (id_pret) REFERENCES ef_pret(id_pret)
);


CREATE TABLE remboursement (
    id_remboursement INT PRIMARY KEY AUTO_INCREMENT,
    id_pret INT NOT NULL,                  
    id_echeance INT,        
    montant DECIMAL(12,2) NOT NULL,
    date_remboursement DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pret) REFERENCES ef_pret(id_pret),
    FOREIGN KEY (id_echeance) REFERENCES ef_echeance_pret(id_echeance)
);

-- 6.7 Historique des fonds

CREATE TABLE ef_historique_transaction(
    id_pret INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    montant DECIMAL(10,2),
    description VARCHAR(100),
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur) REFERENCES ef_utilisateur(id_utilisateur)
);

-- 7. TABLE VALIDATION DE PRET
CREATE TABLE ef_validation_pret (
    id_validation INT AUTO_INCREMENT PRIMARY KEY,
    id_pret INT NOT NULL,
    id_agent INT NOT NULL,
    date_validation DATETIME DEFAULT CURRENT_TIMESTAMP,
    commentaire TEXT,
    FOREIGN KEY (id_pret) REFERENCES ef_pret(id_pret),
    FOREIGN KEY (id_agent) REFERENCES ef_utilisateur(id_utilisateur)
);

-- 8. TABLE AJOUT FONDS
CREATE TABLE ef_ajout_fonds (
    id_ajout INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    montant DECIMAL(15,2) NOT NULL,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur) REFERENCES ef_etablissement_financier(id_etablissement)
);

CREATE TABLE ef_echeancier (
    id_echeancier INT AUTO_INCREMENT PRIMARY KEY,
    id_pret INT NOT NULL,
    numero_echeance INT NOT NULL,
    date_echeance DATE NOT NULL,
    montant DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (id_pret) REFERENCES ef_pret(id_pret)
);

-- 9. INSERTION DE STATUTS PAR DÉFAUT
INSERT INTO ef_statut (libelle) VALUES
('En attente'),
('Validé'),
('Rejeté');

-- 1. Établissement Financier
INSERT INTO ef_etablissement_financier (nom, solde)
VALUES ('EFI Bank', 10000000.00);



-- Supposons que l’ID généré est 1
-- 2. Administrateur
INSERT INTO ef_utilisateur (nom, email, mot_de_passe, role, id_etablissement)
VALUES ('Admin Principal', 'admin@gmail.com', 'admin123', 'admin', 1);

-- 3. Agents
INSERT INTO ef_utilisateur (nom, email, mot_de_passe, role, id_etablissement)
VALUES 
('Agent One', 'agent1@gmail.com', 'agent123', 'agent', 1),
('Agent Two', 'agent2@gmail.com', 'agent456', 'agent', 1);

