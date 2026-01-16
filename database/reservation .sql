-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 16 jan. 2026 à 19:48
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `reservation`
--

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `salle_id` int(11) NOT NULL,
  `date_reservation` date NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `qr_token` varchar(64) NOT NULL,
  `qr_used` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `salle_id`, `date_reservation`, `heure_debut`, `heure_fin`, `created_at`, `qr_token`, `qr_used`) VALUES
(22, 2, 13, '2026-01-16', '03:00:00', '05:00:00', '2026-01-15 21:00:57', '1d27d45e9c1ed3a20e9351b0668053e7', 0);

-- --------------------------------------------------------

--
-- Structure de la table `salles`
--

CREATE TABLE `salles` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `capacite` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `categorie` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `salles`
--

INSERT INTO `salles` (`id`, `nom`, `capacite`, `created_at`, `categorie`, `description`) VALUES
(13, 'Gold', 80, '2026-01-14 18:43:34', 'Conférence', 'Salle de réunion calme et climatisée, équipée d’une grande table, de chaises ergonomiques et d’un écran de projection. Elle est idéale pour les réunions administratives, les conférences, les soutenances et les ateliers de travail en groupe.'),
(14, 'Kalysse', 80, '2026-01-14 18:44:30', 'Conférence', 'Salle de réunion calme et confortable, équipée d’une grande table, de chaises ergonomiques et d’un écran de projection. Elle est idéale pour les réunions administratives, les conférences, les soutenances et les ateliers de travail en groupe.'),
(15, 'Fiddle', 500, '2026-01-14 18:47:32', 'Amphithéâtre', 'Amphithéâtre spacieux conçu pour accueillir un grand nombre d’étudiants. Il est équipé de sièges en gradins, d’un tableau, d’un vidéoprojecteur et d’un système de sonorisation. Cette salle est idéale pour les cours magistraux, les conférences et les présentations publiques.'),
(16, 'Omen', 500, '2026-01-14 18:48:08', 'Amphithéâtre', 'Amphithéâtre spacieux conçu pour accueillir un grand nombre d’étudiants. Il est équipé de sièges en gradins, d’un tableau, d’un vidéoprojecteur et d’un système de sonorisation. Cette salle est idéale pour les cours magistraux, les conférences et les présentations publiques.'),
(17, 'Blue-Path', 15, '2026-01-14 18:49:33', 'Réunion', 'Salle de réunion calme et fonctionnelle, équipée d’une grande table, de chaises confortables et d’un tableau. Elle est adaptée aux réunions administratives, aux travaux de groupe et aux séances de planification.'),
(18, 'Green-Path', 15, '2026-01-14 18:50:07', 'Réunion', 'Salle de réunion calme et fonctionnelle, équipée d’une grande table, de chaises confortables et d’un tableau. Elle est adaptée aux réunions administratives, aux travaux de groupe et aux séances de planification.'),
(19, 'Star', 150, '2026-01-14 18:53:11', 'Mariage', 'Salle de mariage élégante conçue pour accueillir des cérémonies et des réceptions. Elle offre un grand espace modulable, une bonne ventilation et un cadre adapté aux décorations événementielles. Idéale pour les mariages, banquets et célébrations familiales.'),
(20, 'Goty', 1, '2026-01-14 18:55:18', 'Focus', 'Cabine individuelle aménagée pour une seule personne, offrant un environnement calme et intime. Elle est équipée d’un siège, d’une table ou d’un support de travail, et permet une utilisation optimale pour la lecture, l’étude, la prière ou la méditation. Inspirée des salles à usage individuel répandues en Asie.');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(20) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `prenom`, `password`, `created_at`, `role`) VALUES
(2, 'Patrick', 'Olaf', '$2y$10$f60OGe6cyBeYQCqYRJ0i6uK5o0qUsmZTMUd9gpShbZzC.M48A4TMa', '2026-01-13 10:09:50', 'admin'),
(4, 'Ace', 'Chaud', '$2y$10$EIZtg7Mob92m0yR7X5JP/ewDNbNKh94q51PMWsTADXoPlEyqNg9Ry', '2026-01-13 16:10:09', 'user'),
(5, 'Josias', 'Kiss', '$2y$10$AvApFPe6swDIhHgNZL4TDOzS.RnD6MXeXae2K7VUd74pjkfL9gLpS', '2026-01-13 16:15:49', 'user'),
(6, 'christ', 'Sacos', '$2y$10$OoGKFpu/7HxUPu1ISDciY.8UqLlNA1MDmO7WHL3x6eJJmCWzq8xk.', '2026-01-13 16:18:40', 'admin');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_reservation` (`salle_id`,`date_reservation`,`heure_debut`,`heure_fin`),
  ADD KEY `fk_res_user` (`user_id`);

--
-- Index pour la table `salles`
--
ALTER TABLE `salles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `salles`
--
ALTER TABLE `salles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `fk_res_salle` FOREIGN KEY (`salle_id`) REFERENCES `salles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_res_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
