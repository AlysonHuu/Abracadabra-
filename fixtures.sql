-- Nettoyage des données existantes (Optionnel, attention à l'ordre pour les clés étrangères)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE compte_seance;
TRUNCATE TABLE seance;
TRUNCATE TABLE salle;
TRUNCATE TABLE film;
TRUNCATE TABLE compte;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Insertion des Salles
INSERT INTO `salle` (`id`, `nom`, `nb_places`, `created_at`) VALUES
(1, 'Salle Prestige', 50, NOW()),
(2, 'Salle IMAX', 300, NOW()),
(3, 'Salle 3 (Standard)', 120, NOW());

-- 2. Insertion des Films
INSERT INTO `film` (`id`, `affiche`, `nom`, `description`, `genre`, `est_dispo`, `date_sortie`, `duree`, `note`, `created_at`) VALUES
(1, 'avatar_3.jpg', 'Avatar: Fire and Ash', 'La suite des aventures sur Pandora avec le peuple des cendres.', 'Science-Fiction', 1, '2025-12-17', 190, 4.5, NOW()),
(2, 'batman.jpg', 'The Batman 2', 'Le Chevalier Noir affronte de nouveaux défis à Gotham.', 'Action', 1, '2026-10-02', 165, 4.8, NOW()),
(3, 'dune_3.jpg', 'Dune: Messiah', 'L ascension de Paul Atréides sur Arrakis.', 'Aventure', 0, '2026-03-20', 155, 4.9, NOW()),
(4, 'inception.jpg', 'Inception', 'Dom Cobb est un voleur expérimenté dans l art périlleux de l extraction.', 'Thriller', 1, '2010-07-21', 148, 4.7, NOW()),
(5, 'gladiator_2.jpg', 'Gladiator II', 'L héritage de Maximus à travers les yeux de Lucius.', 'Drame', 1, '2024-11-13', 150, 4.2, NOW());

-- 3. Insertion des Séances (Liées aux films et aux salles)
INSERT INTO `seance` (`id`, `date_diffusion`, `nb_place_reservees`, `film_id`, `salle_id`, `created_at`) VALUES
(1, '2026-02-26 20:00:00', 12, 1, 2, NOW()),
(2, '2026-02-26 22:30:00', 0, 1, 2, NOW()),
(3, '2026-02-27 14:00:00', 45, 2, 1, NOW()),
(4, '2026-02-27 18:00:00', 100, 2, 3, NOW()),
(5, '2026-02-28 20:30:00', 5, 4, 1, NOW());

-- 4. Insertion des Comptes (Mots de passe factices hachés - format standard Symfony)
-- Note : statut 1 = Admin, statut 0 = Client
INSERT INTO `compte` (`id`, `email`, `roles`, `password`, `nom`, `prenom`, `statut`, `created_at`) VALUES
(1, 'admin@abracadabra.fr', '["ROLE_ADMIN"]', '$2y$13$qJvOa.Yp6Xj6C5N9R5R7reK4U1K5K5K5K5K5K5K5K5K5K5K5K5K5', 'Boss', 'Hugo', 1, NOW()),
(2, 'client1@gmail.com', '["ROLE_USER"]', '$2y$13$qJvOa.Yp6Xj6C5N9R5R7reK4U1K5K5K5K5K5K5K5K5K5K5K5K5K5', 'Dupont', 'Jean', 0, NOW()),
(3, 'client2@yahoo.fr', '["ROLE_USER"]', '$2y$13$qJvOa.Yp6Xj6C5N9R5R7reK4U1K5K5K5K5K5K5K5K5K5K5K5K5K5', 'Martin', 'Sophie', 0, NOW()),
(4, 'client3@orange.fr', '["ROLE_USER"]', '$2y$13$qJvOa.Yp6Xj6C5N9R5R7reK4U1K5K5K5K5K5K5K5K5K5K5K5K5K5', 'Lefebvre', 'Thomas', 0, NOW()),
(5, 'client4@outlook.com', '["ROLE_USER"]', '$2y$13$qJvOa.Yp6Xj6C5N9R5R7reK4U1K5K5K5K5K5K5K5K5K5K5K5K5K5', 'Bernard', 'Julie', 0, NOW());

-- 5. Insertion des Réservations (Table Pivot compte_seance)
INSERT INTO `compte_seance` (`compte_id`, `seance_id`) VALUES
(2, 1), -- Jean a réservé Avatar
(2, 3), -- Jean a aussi réservé Batman
(3, 1), -- Sophie a réservé Avatar
(4, 4), -- Thomas a réservé Batman
(5, 5); -- Julie a réservé Inception