-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : lun. 27 avr. 2026 à 18:49
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bd_audit`
--

-- --------------------------------------------------------

--
-- Structure de la table `structures`
--

CREATE TABLE `structures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `region_id` bigint(20) UNSIGNED DEFAULT NULL,
  `departement_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `structures`
--

(31, 'Ministère de la Justice, Garde des Sceaux', NULL, NULL, 1, 47, '2025-12-09 08:48:04', '2025-12-09 08:48:04'),
(32, 'Ministère de l’Énergie, du Pétrole et des Mines', NULL, NULL, 1, 47, '2025-12-09 08:48:04', '2025-12-09 08:48:04'),
(33, 'Ministère de l’Intégration Africaine, des Affaires étrangères et des Sénégalais de l’Extérieur', NULL, NULL, 1, 47, '2025-12-09 08:48:04', '2025-12-09 08:48:04'),
(34, 'Ministère des Forces Armées', NULL, NULL, 1, 47, '2025-12-09 08:48:04', '2025-12-09 08:48:04'),
(35, 'Ministère de l’Intérieur et de la Sécurité publique', NULL, NULL, 1, 47, '2025-12-09 08:48:04', '2025-12-09 08:48:04'),
(36, 'Ministère de l’Économie, du Plan et de la Coopération', NULL, NULL, 1, 47, '2025-12-09 08:48:04', '2025-12-09 08:48:04'),
(37, 'Ministère des Finances et du Budget', NULL, NULL, 1, 47, '2025-12-09 08:48:04', '2025-12-09 08:48:04'),
(38, 'Ministère de l’Enseignement supérieur, de la Recherche et de l’Innovation', NULL, NULL, 1, 47, '2025-12-09 08:48:04', '2025-12-09 08:48:04'),
(39, 'Ministère des Transports Terrestres et Aériens', NULL, NULL, 1, 47, '2025-12-09 08:48:04', '2025-12-09 08:48:04'),
(40, 'Ministère de la Communication, des Télécommunications et du Numérique', NULL, NULL, 1, 47, '2025-12-09 08:48:04', '2025-12-09 08:48:04'),
(41, 'Ministère de l’Agriculture, de la Souveraineté Alimentaire et de l’Elevage', NULL, NULL, 1, 47, '2025-12-09 08:48:04', '2025-12-09 08:48:04'),
(42, 'Ministère de l’Hydraulique et de l’Assainissement', NULL, NULL, 1, 47, '2025-12-09 08:48:04', '2025-12-09 08:48:04'),
(43, 'Ministère de la Santé et de l’Hygiène Publique', NULL, NULL, 1, 47, '2025-12-09 08:48:04', '2025-12-09 08:48:04'),
(44, 'Ministère de la Famille, de l’Action sociale et des Solidarités', NULL, NULL, 1, 47, '2025-12-09 08:48:04', '2025-12-09 08:48:04'),
(45, 'Ministère de l’Emploi et de la Formation Professionnelle et Technique', NULL, NULL, 1, 47, '2025-12-09 08:48:04', '2025-12-09 08:48:04'),
(46, 'Ministère de l’Environnement et de la Transition Ecologique', NULL, NULL, 1, 47, '2025-12-09 08:48:04', '2025-12-09 08:48:04'),
(47, 'Ministère de l’Urbanisme, des Collectivités Territoriales et de l’Aménagement des Territoires', NULL, NULL, 1, 47, '2025-12-09 08:48:04', '2025-12-09 08:48:04'),
(48, 'Ministère de l’Industrie et du Commerce', NULL, NULL, 1, 47, '2025-12-09 08:48:04', '2025-12-09 08:48:04'),
(49, 'Ministère des Pêches et de l’Economie Maritime', NULL, NULL, 1, 47, '2025-12-09 08:48:04', '2025-12-09 08:48:04'),
(50, 'Ministère de la Jeunesse et des Sports', NULL, NULL, 1, 47, '2025-12-09 08:48:04', '2025-12-09 08:48:04'),
(51, 'Ministère de la Microfinance et de l’Economie Sociale et Solidaire', NULL, NULL, 1, 47, '2025-12-09 08:48:04', '2025-12-09 08:48:04'),
(52, 'Ministère des Infrastructures', NULL, NULL, 1, 47, '2025-12-09 08:48:04', '2025-12-09 08:48:04'),
(53, 'Ministère de la Culture, de l’Artisanat et du Tourisme', NULL, NULL, 1, 47, '2025-12-09 08:48:04', '2025-12-09 08:48:04'),


--
-- Index pour les tables déchargées
--

--
-- Index pour la table `structures`
--
ALTER TABLE `structures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `structures_parent_id_foreign` (`parent_id`),
  ADD KEY `structures_region_id_foreign` (`region_id`),
  ADD KEY `structures_departement_id_foreign` (`departement_id`),
  ADD KEY `idx_structures_nom_covering` (`nom`,`id`,`parent_id`),
  ADD KEY `idx_structures_nom` (`nom`),
  ADD KEY `idx_structures_parent_id` (`parent_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `structures`
--
ALTER TABLE `structures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `structures`
--
ALTER TABLE `structures`
  ADD CONSTRAINT `structures_departement_id_foreign` FOREIGN KEY (`departement_id`) REFERENCES `departements` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `structures_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `structures` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `structures_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;