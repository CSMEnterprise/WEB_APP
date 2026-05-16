-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 16, 2026 alle 19:55
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
-- Database: `nerdvault`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `account_business`
--

CREATE TABLE `account_business` (
  `id_acc_business` int(11) NOT NULL,
  `id_utente` int(11) NOT NULL,
  `p_iva` varchar(20) NOT NULL,
  `nome_azienda` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `descrizione` text DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email_aziendale` varchar(255) NOT NULL,
  `link_social` varchar(255) DEFAULT NULL,
  `verificato` tinyint(1) NOT NULL DEFAULT 0,
  `data_registrazione` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_admin_verifica` int(11) DEFAULT NULL,
  `data_verifica` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `livello_sicurezza` int(11) NOT NULL DEFAULT 1,
  `data_creazione` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `admin`
--

INSERT INTO `admin` (`id_admin`, `email`, `password_hash`, `livello_sicurezza`, `data_creazione`) VALUES
(2, 'admin@nerdvault.it', '$2y$10$0bhLHjGaBb20aiNx.eeUVeOJYnhz06l6HKbNAm9rG6HWutWE.38Aq', 2, '2026-05-15 13:52:15');

-- --------------------------------------------------------

--
-- Struttura della tabella `annuncio`
--

CREATE TABLE `annuncio` (
  `id_annuncio` int(11) NOT NULL,
  `id_utente` int(11) DEFAULT NULL,
  `id_business` int(11) DEFAULT NULL,
  `id_categoria` int(11) NOT NULL,
  `titolo` varchar(255) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `stato_conservazione` enum('Nuovo','Usato') NOT NULL,
  `prezzo` decimal(10,2) NOT NULL,
  `modalita_consegna` enum('Consegna') NOT NULL,
  `stato` enum('attivo','venduto') NOT NULL DEFAULT 'attivo',
  `data_creazione` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_scadenza` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `annuncio`
--

INSERT INTO `annuncio` (`id_annuncio`, `id_utente`, `id_business`, `id_categoria`, `titolo`, `descrizione`, `stato_conservazione`, `prezzo`, `modalita_consegna`, `stato`, `data_creazione`, `data_scadenza`) VALUES
(4, 2, NULL, 8, 'a', 's', 'Nuovo', 21.00, 'Consegna', 'venduto', '2026-05-15 14:32:27', NULL),
(5, 2, NULL, 8, 'a', 'asd', '', 21.00, 'Consegna', 'attivo', '2026-05-16 11:44:07', NULL),
(6, 2, NULL, 25, 'a', 'dasd', '', 21.00, 'Consegna', 'attivo', '2026-05-16 16:12:21', NULL),
(7, 2, NULL, 30, 'asdasdasda', 'basdasdasdasd', '', 99999999.99, 'Consegna', 'attivo', '2026-05-16 16:12:46', NULL),
(8, 2, NULL, 3, 'fsdfsd', 'sdfsdfsd', '', 99999999.99, 'Consegna', 'attivo', '2026-05-16 16:13:36', NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `carrello`
--

CREATE TABLE `carrello` (
  `id_carrello` int(11) NOT NULL,
  `id_utente` int(11) NOT NULL,
  `data_creazione` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `carrello`
--

INSERT INTO `carrello` (`id_carrello`, `id_utente`, `data_creazione`, `data_aggiornamento`) VALUES
(2, 2, '2026-05-15 13:22:46', '2026-05-15 13:22:46'),
(3, 3, '2026-05-15 14:42:18', '2026-05-15 14:42:18');

-- --------------------------------------------------------

--
-- Struttura della tabella `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `id_padre` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `nome`, `id_padre`) VALUES
(1, 'Manga', NULL),
(2, 'Action Figure', NULL),
(3, 'Fumetti & Graphic Novel', NULL),
(4, 'Videogiochi', NULL),
(5, 'Carte Collezionabili', NULL),
(6, 'Gadget & Merchandise', NULL),
(7, 'Modellismo & Statue', NULL),
(8, 'Abbigliamento', NULL),
(9, 'Libri & Artbook', NULL),
(10, 'Shonen', 1),
(11, 'Shojo', 1),
(12, 'Seinen', 1),
(13, 'Josei', 1),
(14, 'Mecha', 1),
(15, 'Isekai', 1),
(16, 'Horror', 1),
(17, 'Slice of Life', 1),
(18, 'Fantascienza', 1),
(19, 'Anime & Manga Figure', 2),
(20, 'Supereroi', 2),
(21, 'Star Wars', 2),
(22, 'Videogiochi Figure', 2),
(23, 'Film & Serie TV', 2),
(24, 'Mecha & Robot', 2),
(25, 'Funko Pop', 2),
(26, 'Marvel', 3),
(27, 'DC Comics', 3),
(28, 'Fumetti Italiani', 3),
(29, 'Indie & Alternative', 3),
(30, 'Fumetti Europei', 3),
(31, 'PlayStation', 4),
(32, 'Xbox', 4),
(33, 'Nintendo', 4),
(34, 'PC Gaming', 4),
(35, 'Retrogaming', 4),
(36, 'Pokémon TCG', 5),
(37, 'Yu-Gi-Oh!', 5),
(38, 'Magic: The Gathering', 5),
(39, 'Dragon Ball Super Card', 5),
(40, 'One Piece Card Game', 5),
(41, 'Portachiavi & Spille', 6),
(42, 'Poster & Stampe', 6),
(43, 'Tazze & Gadget', 6),
(44, 'Peluche', 6),
(45, 'Borse & Zaini', 6),
(46, 'Gunpla & Modellismo', 7),
(47, 'Figure Premium', 7),
(48, 'Statue da Collezione', 7),
(49, 'Diorami', 7),
(50, 'T-Shirt', 8),
(51, 'Felpe & Cappellini', 8),
(52, 'Cosplay', 8),
(53, 'Accessori Moda', 8),
(54, 'Artbook', 9),
(55, 'Light Novel', 9),
(56, 'Guide & Enciclopedie', 9),
(57, 'Romanzi', 9);

-- --------------------------------------------------------

--
-- Struttura della tabella `elemento_carrello`
--

CREATE TABLE `elemento_carrello` (
  `id_elemento_carrello` int(11) NOT NULL,
  `id_carrello` int(11) NOT NULL,
  `id_annuncio` int(11) NOT NULL,
  `data_aggiunta` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `feedback`
--

CREATE TABLE `feedback` (
  `id_feedback` int(11) NOT NULL,
  `id_autore` int(11) NOT NULL,
  `id_pagamento` int(11) NOT NULL,
  `valutazione` tinyint(4) NOT NULL,
  `commento` text DEFAULT NULL,
  `data_feedback` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `feedback`
--

INSERT INTO `feedback` (`id_feedback`, `id_autore`, `id_pagamento`, `valutazione`, `commento`, `data_feedback`) VALUES
(1, 3, 1, 5, 'Ngulo che merda', '2026-05-15 14:51:48');

-- --------------------------------------------------------

--
-- Struttura della tabella `immagine`
--

CREATE TABLE `immagine` (
  `id_immagine` int(11) NOT NULL,
  `id_annuncio` int(11) NOT NULL,
  `url` varchar(500) NOT NULL,
  `ordine` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `immagine`
--

INSERT INTO `immagine` (`id_immagine`, `id_annuncio`, `url`, `ordine`) VALUES
(4, 4, 'uploads/annunci/4/f7f467169f6299e0c92f624d38dafc65.png', 0),
(5, 5, 'uploads/annunci/5/bf137682b9a44f7c0a291249be530bc6.jpg', 0),
(6, 7, 'uploads/annunci/7/039ced1e82ea71ff314a2cb006781231.png', 0),
(7, 8, 'uploads/annunci/8/3d22ab1fe5e4cdab95592e590662a96a.png', 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `indirizzi`
--

CREATE TABLE `indirizzi` (
  `id_indirizzo` int(11) NOT NULL,
  `id_utente` int(11) DEFAULT NULL,
  `id_business` int(11) DEFAULT NULL,
  `tipo` enum('casa','lavoro','fatturazione','spedizione') DEFAULT 'casa',
  `via` varchar(100) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `cap` char(5) DEFAULT NULL,
  `citta` varchar(100) DEFAULT NULL,
  `provincia` char(2) DEFAULT NULL,
  `paese` varchar(50) NOT NULL DEFAULT 'Italia',
  `predefinito` tinyint(1) NOT NULL DEFAULT 0
) ;

--
-- Dump dei dati per la tabella `indirizzi`
--

INSERT INTO `indirizzi` (`id_indirizzo`, `id_utente`, `id_business`, `tipo`, `via`, `numero`, `cap`, `citta`, `provincia`, `paese`, `predefinito`) VALUES
(4, 2, NULL, 'spedizione', 'Via Asmara', '39', '67100', 'L\'Aquila', 'AQ', 'Italia', 1),
(6, 2, NULL, 'casa', 'Via Asmara', '39', '67100', 'L\'Aquila', 'AQ', 'Italia', 0),
(7, 3, NULL, 'casa', 'Via Asmara', '39', '67100', 'L\'Aquila', 'AQ', 'Italia', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `modera`
--

CREATE TABLE `modera` (
  `id_moderazione` int(11) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `id_utente` int(11) DEFAULT NULL,
  `id_feedback` int(11) DEFAULT NULL,
  `id_annuncio` int(11) DEFAULT NULL,
  `id_business` int(11) DEFAULT NULL,
  `azione_compiuta` text NOT NULL,
  `data_azione` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `pagamento`
--

CREATE TABLE `pagamento` (
  `id_pagamento` int(11) NOT NULL,
  `id_annuncio` int(11) NOT NULL,
  `id_acquirente` int(11) NOT NULL,
  `importo_totale` decimal(10,2) NOT NULL,
  `stato` enum('In_attesa','Completato','Annullato','Rimborsato') NOT NULL DEFAULT 'In_attesa',
  `paypal_transaction_id` varchar(255) DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `pagamento`
--

INSERT INTO `pagamento` (`id_pagamento`, `id_annuncio`, `id_acquirente`, `importo_totale`, `stato`, `paypal_transaction_id`, `data`) VALUES
(1, 4, 3, 21.00, 'Completato', 'asd', '2026-05-15 14:44:26');

-- --------------------------------------------------------

--
-- Struttura della tabella `password_reset`
--

CREATE TABLE `password_reset` (
  `id_reset` int(11) NOT NULL,
  `id_utente` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `scadenza` datetime NOT NULL,
  `usato` tinyint(1) NOT NULL DEFAULT 0,
  `creato_il` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `password_reset`
--

INSERT INTO `password_reset` (`id_reset`, `id_utente`, `token`, `scadenza`, `usato`, `creato_il`) VALUES
(1, 12, '863c8de06636662ae472d7f9e0fb75bbdfb61ee16b79f3dbbeeaeb67ad8a12f8', '2026-05-16 20:51:13', 1, '2026-05-16 19:51:13'),
(2, 12, '544e895b6aac8b6f95ab6c5f6ec90e4deb1a440b06b514b0b858bfee4bf1ba43', '2026-05-16 20:52:26', 1, '2026-05-16 19:52:26');

-- --------------------------------------------------------

--
-- Struttura della tabella `preferito`
--

CREATE TABLE `preferito` (
  `id_utente` int(11) NOT NULL,
  `id_annuncio` int(11) NOT NULL,
  `data_aggiunta` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `segnalazione`
--

CREATE TABLE `segnalazione` (
  `id_segnalazione` int(11) NOT NULL,
  `id_segnalante` int(11) NOT NULL,
  `id_annuncio` int(11) DEFAULT NULL,
  `id_utente_segnalato` int(11) DEFAULT NULL,
  `id_business` int(11) DEFAULT NULL,
  `id_feedback` int(11) DEFAULT NULL,
  `tipologia` enum('Spam','Truffa','Contenuto_inappropriato','Altro') NOT NULL,
  `descrizione` text DEFAULT NULL,
  `stato` enum('Aperta','In_revisione','Risolta') NOT NULL DEFAULT 'Aperta',
  `data_segnalazione` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_admin` int(11) DEFAULT NULL,
  `data_risoluzione` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `utente_registrato`
--

CREATE TABLE `utente_registrato` (
  `id_utente` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verificata` tinyint(1) NOT NULL DEFAULT 0,
  `token_verifica` varchar(64) DEFAULT NULL,
  `token_scadenza` datetime DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `propic` varchar(255) DEFAULT NULL,
  `stato_ban` tinyint(1) NOT NULL DEFAULT 0,
  `data_registrazione` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `utente_registrato`
--

INSERT INTO `utente_registrato` (`id_utente`, `email`, `email_verificata`, `token_verifica`, `token_scadenza`, `username`, `password_hash`, `nome`, `telefono`, `propic`, `stato_ban`, `data_registrazione`) VALUES
(2, 'a@gmail.com', 1, NULL, NULL, 'a', '$2y$10$Rggdvl86uMH5dWy0acCQGuNHxBzH6NFHod0r2Dq1WXp4SElriKpR.', 'Paolo Calabrese', '2', 'uploads/propic/user_2_4b0ac4145b5df87a.jpg', 0, '2026-05-15 12:21:24'),
(3, 'c@gmail.com', 1, NULL, NULL, 'c', '$2y$10$B9wSxWfGTfArHE.uUE.Sdeo026aoZRupewjQ5M.WPJLJArRg5t.6u', 'Paolo Calabrese', '2', NULL, 0, '2026-05-15 13:54:41'),
(12, 'accpakun99@gmail.com', 1, NULL, NULL, 'KiritoKun', '$2y$10$XJ1axsWkO.wcb7gLO4hCuOlDzdvz/0Y5LEC19aTfdYppQBhGIc8jO', NULL, '3891925959', NULL, 0, '2026-05-16 17:44:01');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `account_business`
--
ALTER TABLE `account_business`
  ADD PRIMARY KEY (`id_acc_business`),
  ADD UNIQUE KEY `id_utente` (`id_utente`),
  ADD UNIQUE KEY `p_iva` (`p_iva`),
  ADD UNIQUE KEY `email_aziendale` (`email_aziendale`),
  ADD KEY `id_admin_verifica` (`id_admin_verifica`);

--
-- Indici per le tabelle `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indici per le tabelle `annuncio`
--
ALTER TABLE `annuncio`
  ADD PRIMARY KEY (`id_annuncio`),
  ADD KEY `id_utente` (`id_utente`),
  ADD KEY `id_business` (`id_business`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indici per le tabelle `carrello`
--
ALTER TABLE `carrello`
  ADD PRIMARY KEY (`id_carrello`),
  ADD UNIQUE KEY `id_utente` (`id_utente`);

--
-- Indici per le tabelle `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nome` (`nome`),
  ADD KEY `id_padre` (`id_padre`);

--
-- Indici per le tabelle `elemento_carrello`
--
ALTER TABLE `elemento_carrello`
  ADD PRIMARY KEY (`id_elemento_carrello`),
  ADD UNIQUE KEY `id_carrello` (`id_carrello`,`id_annuncio`),
  ADD KEY `id_annuncio` (`id_annuncio`);

--
-- Indici per le tabelle `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id_feedback`),
  ADD UNIQUE KEY `id_pagamento` (`id_pagamento`),
  ADD KEY `id_autore` (`id_autore`);

--
-- Indici per le tabelle `immagine`
--
ALTER TABLE `immagine`
  ADD PRIMARY KEY (`id_immagine`),
  ADD UNIQUE KEY `id_annuncio` (`id_annuncio`,`ordine`);

--
-- Indici per le tabelle `indirizzi`
--
ALTER TABLE `indirizzi`
  ADD PRIMARY KEY (`id_indirizzo`),
  ADD KEY `id_utente` (`id_utente`),
  ADD KEY `id_business` (`id_business`);

--
-- Indici per le tabelle `modera`
--
ALTER TABLE `modera`
  ADD PRIMARY KEY (`id_moderazione`),
  ADD KEY `id_admin` (`id_admin`),
  ADD KEY `id_utente` (`id_utente`),
  ADD KEY `id_feedback` (`id_feedback`),
  ADD KEY `id_annuncio` (`id_annuncio`),
  ADD KEY `id_business` (`id_business`);

--
-- Indici per le tabelle `pagamento`
--
ALTER TABLE `pagamento`
  ADD PRIMARY KEY (`id_pagamento`),
  ADD UNIQUE KEY `paypal_transaction_id` (`paypal_transaction_id`),
  ADD KEY `id_annuncio` (`id_annuncio`),
  ADD KEY `id_acquirente` (`id_acquirente`);

--
-- Indici per le tabelle `password_reset`
--
ALTER TABLE `password_reset`
  ADD PRIMARY KEY (`id_reset`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `id_utente` (`id_utente`);

--
-- Indici per le tabelle `preferito`
--
ALTER TABLE `preferito`
  ADD PRIMARY KEY (`id_utente`,`id_annuncio`),
  ADD KEY `id_annuncio` (`id_annuncio`);

--
-- Indici per le tabelle `segnalazione`
--
ALTER TABLE `segnalazione`
  ADD PRIMARY KEY (`id_segnalazione`),
  ADD KEY `id_segnalante` (`id_segnalante`),
  ADD KEY `id_utente_segnalato` (`id_utente_segnalato`),
  ADD KEY `id_admin` (`id_admin`),
  ADD KEY `id_feedback` (`id_feedback`),
  ADD KEY `id_annuncio` (`id_annuncio`),
  ADD KEY `id_business` (`id_business`);

--
-- Indici per le tabelle `utente_registrato`
--
ALTER TABLE `utente_registrato`
  ADD PRIMARY KEY (`id_utente`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `account_business`
--
ALTER TABLE `account_business`
  MODIFY `id_acc_business` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `annuncio`
--
ALTER TABLE `annuncio`
  MODIFY `id_annuncio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT per la tabella `carrello`
--
ALTER TABLE `carrello`
  MODIFY `id_carrello` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT per la tabella `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT per la tabella `elemento_carrello`
--
ALTER TABLE `elemento_carrello`
  MODIFY `id_elemento_carrello` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT per la tabella `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id_feedback` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `immagine`
--
ALTER TABLE `immagine`
  MODIFY `id_immagine` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT per la tabella `indirizzi`
--
ALTER TABLE `indirizzi`
  MODIFY `id_indirizzo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `modera`
--
ALTER TABLE `modera`
  MODIFY `id_moderazione` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `pagamento`
--
ALTER TABLE `pagamento`
  MODIFY `id_pagamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `password_reset`
--
ALTER TABLE `password_reset`
  MODIFY `id_reset` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `segnalazione`
--
ALTER TABLE `segnalazione`
  MODIFY `id_segnalazione` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `utente_registrato`
--
ALTER TABLE `utente_registrato`
  MODIFY `id_utente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `account_business`
--
ALTER TABLE `account_business`
  ADD CONSTRAINT `account_business_ibfk_1` FOREIGN KEY (`id_utente`) REFERENCES `utente_registrato` (`id_utente`) ON DELETE CASCADE,
  ADD CONSTRAINT `account_business_ibfk_2` FOREIGN KEY (`id_admin_verifica`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL;

--
-- Limiti per la tabella `annuncio`
--
ALTER TABLE `annuncio`
  ADD CONSTRAINT `annuncio_ibfk_1` FOREIGN KEY (`id_utente`) REFERENCES `utente_registrato` (`id_utente`) ON DELETE CASCADE,
  ADD CONSTRAINT `annuncio_ibfk_2` FOREIGN KEY (`id_business`) REFERENCES `account_business` (`id_acc_business`) ON DELETE CASCADE,
  ADD CONSTRAINT `annuncio_ibfk_3` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`);

--
-- Limiti per la tabella `carrello`
--
ALTER TABLE `carrello`
  ADD CONSTRAINT `carrello_ibfk_1` FOREIGN KEY (`id_utente`) REFERENCES `utente_registrato` (`id_utente`) ON DELETE CASCADE;

--
-- Limiti per la tabella `categoria`
--
ALTER TABLE `categoria`
  ADD CONSTRAINT `categoria_ibfk_1` FOREIGN KEY (`id_padre`) REFERENCES `categoria` (`id_categoria`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `elemento_carrello`
--
ALTER TABLE `elemento_carrello`
  ADD CONSTRAINT `elemento_carrello_ibfk_1` FOREIGN KEY (`id_carrello`) REFERENCES `carrello` (`id_carrello`) ON DELETE CASCADE,
  ADD CONSTRAINT `elemento_carrello_ibfk_2` FOREIGN KEY (`id_annuncio`) REFERENCES `annuncio` (`id_annuncio`) ON DELETE CASCADE;

--
-- Limiti per la tabella `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`id_autore`) REFERENCES `utente_registrato` (`id_utente`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`id_pagamento`) REFERENCES `pagamento` (`id_pagamento`) ON DELETE CASCADE;

--
-- Limiti per la tabella `immagine`
--
ALTER TABLE `immagine`
  ADD CONSTRAINT `immagine_ibfk_1` FOREIGN KEY (`id_annuncio`) REFERENCES `annuncio` (`id_annuncio`) ON DELETE CASCADE;

--
-- Limiti per la tabella `indirizzi`
--
ALTER TABLE `indirizzi`
  ADD CONSTRAINT `indirizzi_ibfk_1` FOREIGN KEY (`id_utente`) REFERENCES `utente_registrato` (`id_utente`) ON DELETE CASCADE,
  ADD CONSTRAINT `indirizzi_ibfk_2` FOREIGN KEY (`id_business`) REFERENCES `account_business` (`id_acc_business`) ON DELETE CASCADE;

--
-- Limiti per la tabella `modera`
--
ALTER TABLE `modera`
  ADD CONSTRAINT `modera_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`),
  ADD CONSTRAINT `modera_ibfk_2` FOREIGN KEY (`id_utente`) REFERENCES `utente_registrato` (`id_utente`) ON DELETE CASCADE,
  ADD CONSTRAINT `modera_ibfk_3` FOREIGN KEY (`id_feedback`) REFERENCES `feedback` (`id_feedback`) ON DELETE CASCADE,
  ADD CONSTRAINT `modera_ibfk_4` FOREIGN KEY (`id_annuncio`) REFERENCES `annuncio` (`id_annuncio`) ON DELETE CASCADE,
  ADD CONSTRAINT `modera_ibfk_5` FOREIGN KEY (`id_business`) REFERENCES `account_business` (`id_acc_business`) ON DELETE CASCADE;

--
-- Limiti per la tabella `pagamento`
--
ALTER TABLE `pagamento`
  ADD CONSTRAINT `pagamento_ibfk_1` FOREIGN KEY (`id_annuncio`) REFERENCES `annuncio` (`id_annuncio`),
  ADD CONSTRAINT `pagamento_ibfk_2` FOREIGN KEY (`id_acquirente`) REFERENCES `utente_registrato` (`id_utente`);

--
-- Limiti per la tabella `password_reset`
--
ALTER TABLE `password_reset`
  ADD CONSTRAINT `password_reset_ibfk_1` FOREIGN KEY (`id_utente`) REFERENCES `utente_registrato` (`id_utente`) ON DELETE CASCADE;

--
-- Limiti per la tabella `preferito`
--
ALTER TABLE `preferito`
  ADD CONSTRAINT `preferito_ibfk_1` FOREIGN KEY (`id_utente`) REFERENCES `utente_registrato` (`id_utente`) ON DELETE CASCADE,
  ADD CONSTRAINT `preferito_ibfk_2` FOREIGN KEY (`id_annuncio`) REFERENCES `annuncio` (`id_annuncio`) ON DELETE CASCADE;

--
-- Limiti per la tabella `segnalazione`
--
ALTER TABLE `segnalazione`
  ADD CONSTRAINT `segnalazione_ibfk_1` FOREIGN KEY (`id_segnalante`) REFERENCES `utente_registrato` (`id_utente`) ON DELETE CASCADE,
  ADD CONSTRAINT `segnalazione_ibfk_2` FOREIGN KEY (`id_utente_segnalato`) REFERENCES `utente_registrato` (`id_utente`) ON DELETE CASCADE,
  ADD CONSTRAINT `segnalazione_ibfk_3` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL,
  ADD CONSTRAINT `segnalazione_ibfk_4` FOREIGN KEY (`id_feedback`) REFERENCES `feedback` (`id_feedback`) ON DELETE CASCADE,
  ADD CONSTRAINT `segnalazione_ibfk_5` FOREIGN KEY (`id_annuncio`) REFERENCES `annuncio` (`id_annuncio`) ON DELETE CASCADE,
  ADD CONSTRAINT `segnalazione_ibfk_6` FOREIGN KEY (`id_business`) REFERENCES `account_business` (`id_acc_business`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
