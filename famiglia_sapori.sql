-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 20 nov. 2025 à 11:29
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
-- Base de données : `famiglia_sapori`
--

-- --------------------------------------------------------

--
-- Structure de la table `ordine`
--

CREATE TABLE `ordine` (
  `id_ordine` int(11) NOT NULL,
  `id_tavolo` int(11) NOT NULL,
  `data_ora` datetime NOT NULL,
  `stato` enum('inviato','in preparazione','pronto') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ordine_piatto`
--

CREATE TABLE `ordine_piatto` (
  `id_ordine_piatto` int(11) NOT NULL,
  `id_ordine` int(11) NOT NULL,
  `id_piatto` int(11) NOT NULL,
  `quantita` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `piatto`
--

CREATE TABLE `piatto` (
  `id_piatto` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `prezzo` decimal(5,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tavolo`
--

CREATE TABLE `tavolo` (
  `id_tavolo` int(11) NOT NULL,
  `numero` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `ordine`
--
ALTER TABLE `ordine`
  ADD PRIMARY KEY (`id_ordine`),
  ADD KEY `id_tavolo` (`id_tavolo`);

--
-- Index pour la table `ordine_piatto`
--
ALTER TABLE `ordine_piatto`
  ADD PRIMARY KEY (`id_ordine_piatto`),
  ADD KEY `id_ordine` (`id_ordine`),
  ADD KEY `id_piatto` (`id_piatto`);

--
-- Index pour la table `piatto`
--
ALTER TABLE `piatto`
  ADD PRIMARY KEY (`id_piatto`);

--
-- Index pour la table `tavolo`
--
ALTER TABLE `tavolo`
  ADD PRIMARY KEY (`id_tavolo`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `ordine`
--
ALTER TABLE `ordine`
  MODIFY `id_ordine` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `ordine_piatto`
--
ALTER TABLE `ordine_piatto`
  MODIFY `id_ordine_piatto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `piatto`
--
ALTER TABLE `piatto`
  MODIFY `id_piatto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tavolo`
--
ALTER TABLE `tavolo`
  MODIFY `id_tavolo` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `ordine`
--
ALTER TABLE `ordine`
  ADD CONSTRAINT `ordine_ibfk_1` FOREIGN KEY (`id_tavolo`) REFERENCES `tavolo` (`id_tavolo`);

--
-- Contraintes pour la table `ordine_piatto`
--
ALTER TABLE `ordine_piatto`
  ADD CONSTRAINT `ordine_piatto_ibfk_1` FOREIGN KEY (`id_ordine`) REFERENCES `ordine` (`id_ordine`),
  ADD CONSTRAINT `ordine_piatto_ibfk_2` FOREIGN KEY (`id_piatto`) REFERENCES `piatto` (`id_piatto`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
