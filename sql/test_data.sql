-- Jeu de données de test pour projet gestion de prêts

-- 1. Établissement financier
INSERT INTO ef_etablissement_financier (nom, solde) VALUES
('EFI Bank', 10000000.00);

-- 2. Statuts
-- INSERT INTO ef_statut (id_statut, libelle) VALUES
-- (1, 'En attente'),
-- (2, 'Validé'),
-- (3, 'Rejeté');

-- 3. Utilisateurs (admin + agents)
INSERT INTO ef_utilisateur (id_utilisateur, nom, email, mot_de_passe, role, id_etablissement) VALUES
(1, 'Admin Principal', 'admin@efibank.com', 'admin123', 'admin', 1),
(2, 'Agent One', 'agent1@efibank.com', 'agent123', 'agent', 1),
(3, 'Agent Two', 'agent2@efibank.com', 'agent456', 'agent', 1);

-- 4. Clients (étudiants)
INSERT INTO etudiant (id, nom, prenom, email, age) VALUES
(1, 'Rakoto', 'Jean', 'rakoto.jean@email.com', 22),
(2, 'Rasoa', 'Marie', 'rasoa.marie@email.com', 24),
(3, 'Randria', 'Paul', 'randria.paul@email.com', 23);

-- 5. Types de prêt
INSERT INTO ef_type_pret (id_type_pret, nom, taux_annuel, duree_max, montant_min, montant_max, id_etablissement) VALUES
(1, 'Conso', 10, 24, 500000, 2000000, 1),
(2, 'Immo', 7, 36, 1000000, 5000000, 1);

-- -- 6. Prêts
-- INSERT INTO ef_pret (id_pret, id_client, id_type_pret, montant, duree, date_demande, id_statut, id_agent) VALUES
-- (1, 1, 1, 1000000, 12, '2025-01-10', 2, 2), -- Validé
-- (2, 2, 2, 2000000, 24, '2025-02-15', 2, 3), -- Validé
-- (3, 3, 1, 1500000, 18, '2025-03-20', 1, 2), -- En attente
-- (4, 1, 2, 1200000, 12, '2025-04-05', 3, 3); -- Rejeté
