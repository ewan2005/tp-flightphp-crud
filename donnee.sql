--JEU DE DONNEE DE LA FONCTIONNALITE INTERET

-- Types de prêt
INSERT INTO ef_type_pret (id_type_pret, nom, taux_annuel, duree_max, montant_min, montant_max, id_etablissement) VALUES
(1, 'Conso', 10, 24, 500000, 2000000, 1),
(2, 'Immo', 7, 36, 1000000, 5000000, 1);

-- Prêts validés
INSERT INTO ef_pret (id_pret, id_client, id_type_pret, montant, duree, date_demande, id_statut, id_agent) VALUES
(1, 1, 1, 1000000, 12, '2025-01-10', 2, 2),
(2, 2, 2, 2000000, 24, '2025-02-15', 2, 3),
(3, 3, 1, 1500000, 18, '2025-03-20', 2, 2),
(4, 1, 2, 1200000, 12, '2025-04-05', 2, 3);