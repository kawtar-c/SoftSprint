-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Gen 08, 2026 alle 10:11
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `famiglia_sapori`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `ordine`
--

CREATE TABLE `ordine` (
  `id_ordine` int(11) NOT NULL,
  `id_tavolo` int(11) NOT NULL,
  `data_ora` datetime NOT NULL,
  `stato` enum('inviato','in preparazione','pronto','completato') NOT NULL,
  `id_utente` int(11) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `totale` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `ordine`
--

INSERT INTO `ordine` (`id_ordine`, `id_tavolo`, `data_ora`, `stato`, `id_utente`, `note`, `totale`) VALUES
(123, 1, '2026-01-07 20:55:58', 'completato', 1, '', 65),
(124, 1, '2026-01-07 20:56:46', 'completato', 1, '', 27),
(125, 3, '2026-01-07 20:57:03', 'completato', 1, '', 46);

-- --------------------------------------------------------

--
-- Struttura della tabella `ordine_piatto`
--

CREATE TABLE `ordine_piatto` (
  `id_ordine_piatto` int(11) NOT NULL,
  `id_ordine` int(11) NOT NULL,
  `id_piatto` int(11) NOT NULL,
  `quantita` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `ordine_piatto`
--

INSERT INTO `ordine_piatto` (`id_ordine_piatto`, `id_ordine`, `id_piatto`, `quantita`) VALUES
(20, 123, 6, 1),
(21, 123, 9, 1),
(22, 123, 12, 1),
(23, 124, 11, 1),
(24, 124, 9, 2),
(25, 124, 14, 1),
(26, 125, 7, 2),
(27, 125, 11, 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `piatto`
--

CREATE TABLE `piatto` (
  `id_piatto` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `prezzo` decimal(5,0) NOT NULL,
  `categoria` varchar(255) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `descrizione` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `piatto`
--

INSERT INTO `piatto` (`id_piatto`, `nome`, `prezzo`, `categoria`, `img`, `descrizione`) VALUES
(6, 'Chitarra alla teramana', 10, 'Primi', 'https://i.pinimg.com/736x/05/42/38/054238558776a09b37fa67edddf633f3.jpg', 'Pasta fresca con pallottine di carne, pomodoro e pecorino abruzzese.'),
(7, 'Scrippelle mbusse', 17, 'Primi', 'https://i.pinimg.com/736x/e0/70/72/e0707213a272487992f475c4f8bc9fbe.jpg', 'Crepes sottilissime arrotolate e servite in brodo di carne profumato.'),
(8, 'Timballo abruzzese', 10, 'Primi', 'https://i.pinimg.com/1200x/b1/f7/82/b1f7827be51a217964da3f21c884f556.jpg', 'Strati di scrippelle con ragù, pallottine, formaggio e verdure, tipico delle feste abruzzesi.'),
(9, 'Mazzarelle teramane', 5, 'Secondi', 'https://i.pinimg.com/736x/4c/a4/e0/4ca4e091c989a72f94814da385b10815.jpg', 'Involtini di interiora d’agnello avvolti in foglie di indivia e cotti lentamente con vino e aromi.'),
(10, 'Arrosticini', 10, 'Secondi', 'https://i.pinimg.com/1200x/0f/d4/ba/0fd4bad4d4cd26fa5c514b6316da80a0.jpg', 'Spiedini tradizionali di pecora cotti alla brace, serviti con pane e olio abruzzese.'),
(11, 'Pecora alla callara', 12, 'Secondi', 'https://i.pinimg.com/1200x/98/7c/0f/987c0f6c768ac35589b8159fa5634411.jpg', 'Carne di pecora cotta lentamente in pentola con patate, aromi di montagna e spezie tradizionali.'),
(12, 'Pizza dolce abruzzese', 50, 'Dolci', 'https://i.pinimg.com/1200x/b3/ef/19/b3ef19d619f645b50a480df3314bd081.jpg', 'Dolce tradizionale a strati con pan di Spagna, alchermes, crema, cioccolato e decorazioni di mandorle.'),
(14, 'Ferratelle', 5, 'Dolci', 'https://i.pinimg.com/736x/c3/e8/d7/c3e8d71106a55c6d7969e0fe1553a56d.jpg', 'Cialde sottili profumate al limone o anice, cotte sull’antico ferro abruzzese.');

-- --------------------------------------------------------

--
-- Struttura della tabella `prenotazione`
--

CREATE TABLE `prenotazione` (
  `nome` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `data` varchar(255) DEFAULT NULL,
  `persone` int(11) DEFAULT NULL,
  `fascia_oraria` varchar(255) DEFAULT NULL,
  `id_prenotazione` int(11) NOT NULL,
  `id_tavolo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `prenotazione`
--

INSERT INTO `prenotazione` (`nome`, `telefono`, `data`, `persone`, `fascia_oraria`, `id_prenotazione`, `id_tavolo`) VALUES
('mario', '3', '2025-12-16', 3, '19:30', 1, NULL),
('mario', '3', '2025-12-16', 8, '20:30', 2, NULL),
('mario', '3', '2025-12-16', 3, '20:30', 3, NULL),
('mario', '3', '2025-12-16', 3, '12:00', 4, NULL),
('mario', '10', '2025-12-16', 10, '14:00', 5, NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `tavolo`
--

CREATE TABLE `tavolo` (
  `id_tavolo` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `capacita_max` int(11) NOT NULL DEFAULT 2,
  `stato` enum('libero','prenotato') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `tavolo`
--

INSERT INTO `tavolo` (`id_tavolo`, `numero`, `capacita_max`, `stato`) VALUES
(1, 1, 2, 'libero'),
(2, 2, 2, 'libero'),
(3, 3, 2, 'libero');

-- --------------------------------------------------------

--
-- Struttura della tabella `utente`
--

CREATE TABLE `utente` (
  `id_utente` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `cognome` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `ruolo` enum('cameriere','cuoco','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `utente`
--

INSERT INTO `utente` (`id_utente`, `nome`, `cognome`, `email`, `password`, `ruolo`) VALUES
(1, 'mario', 'rossi', 'm.rossi@uni.it', '1234', 'cameriere'),
(2, 'nico', 'franchi', 'nich@uni.it', '1234', 'cuoco'),
(7, 'admin', 'admin', 'admin@uni.it', 'admin', 'admin');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `ordine`
--
ALTER TABLE `ordine`
  ADD PRIMARY KEY (`id_ordine`),
  ADD KEY `id_tavolo` (`id_tavolo`),
  ADD KEY `fk_ordine_utente` (`id_utente`);

--
-- Indici per le tabelle `ordine_piatto`
--
ALTER TABLE `ordine_piatto`
  ADD PRIMARY KEY (`id_ordine_piatto`),
  ADD KEY `id_ordine` (`id_ordine`),
  ADD KEY `id_piatto` (`id_piatto`);

--
-- Indici per le tabelle `piatto`
--
ALTER TABLE `piatto`
  ADD PRIMARY KEY (`id_piatto`);

--
-- Indici per le tabelle `prenotazione`
--
ALTER TABLE `prenotazione`
  ADD PRIMARY KEY (`id_prenotazione`),
  ADD KEY `fk_tavolo` (`id_tavolo`);

--
-- Indici per le tabelle `tavolo`
--
ALTER TABLE `tavolo`
  ADD PRIMARY KEY (`id_tavolo`);

--
-- Indici per le tabelle `utente`
--
ALTER TABLE `utente`
  ADD PRIMARY KEY (`id_utente`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `ordine`
--
ALTER TABLE `ordine`
  MODIFY `id_ordine` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT per la tabella `ordine_piatto`
--
ALTER TABLE `ordine_piatto`
  MODIFY `id_ordine_piatto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT per la tabella `piatto`
--
ALTER TABLE `piatto`
  MODIFY `id_piatto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT per la tabella `prenotazione`
--
ALTER TABLE `prenotazione`
  MODIFY `id_prenotazione` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT per la tabella `tavolo`
--
ALTER TABLE `tavolo`
  MODIFY `id_tavolo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT per la tabella `utente`
--
ALTER TABLE `utente`
  MODIFY `id_utente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `ordine`
--
ALTER TABLE `ordine`
  ADD CONSTRAINT `fk_ordine_utente` FOREIGN KEY (`id_utente`) REFERENCES `utente` (`id_utente`),
  ADD CONSTRAINT `ordine_ibfk_1` FOREIGN KEY (`id_tavolo`) REFERENCES `tavolo` (`id_tavolo`);

--
-- Limiti per la tabella `ordine_piatto`
--
ALTER TABLE `ordine_piatto`
  ADD CONSTRAINT `ordine_piatto_ibfk_1` FOREIGN KEY (`id_ordine`) REFERENCES `ordine` (`id_ordine`),
  ADD CONSTRAINT `ordine_piatto_ibfk_2` FOREIGN KEY (`id_piatto`) REFERENCES `piatto` (`id_piatto`);

--
-- Limiti per la tabella `prenotazione`
--
ALTER TABLE `prenotazione`
  ADD CONSTRAINT `fk_tavolo` FOREIGN KEY (`id_tavolo`) REFERENCES `tavolo` (`id_tavolo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
