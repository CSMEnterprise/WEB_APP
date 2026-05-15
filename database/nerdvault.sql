-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 15, 2026 alle 13:12
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
  `stato_conservazione` enum('Nuovo','Usato come nuovo', 'Ottimo', 'Buono', 'Discreto', 'Scarso') NOT NULL,
  `prezzo` decimal(10,2) NOT NULL,
  `modalita_consegna` enum('Consegna') NOT NULL,
  `stato` enum('attivo','venduto') NOT NULL DEFAULT 'attivo',
  `data_creazione` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_scadenza` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Struttura della tabella `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `id_padre` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Struttura della tabella `indirizzi`
--

CREATE TABLE `indirizzi` (
  `id_indirizzo` int(11) NOT NULL,
  `id_utente` int(11) DEFAULT NULL,
  `id_business` int(11) DEFAULT NULL,
  `via` varchar(100) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `cap` char(5) DEFAULT NULL,
  `citta` varchar(100) DEFAULT NULL,
  `provincia` char(2) DEFAULT NULL,
  `paese` varchar(50) NOT NULL DEFAULT 'Italia',
  `predefinito` tinyint(1) NOT NULL DEFAULT 0
) ;

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
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `propic` varchar(255) DEFAULT NULL,
  `stato_ban` tinyint(1) NOT NULL DEFAULT 0,
  `data_registrazione` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `id_acc_business` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `annuncio`
--
ALTER TABLE `annuncio`
  MODIFY `id_annuncio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `carrello`
--
ALTER TABLE `carrello`
  MODIFY `id_carrello` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `elemento_carrello`
--
ALTER TABLE `elemento_carrello`
  MODIFY `id_elemento_carrello` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id_feedback` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `immagine`
--
ALTER TABLE `immagine`
  MODIFY `id_immagine` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id_pagamento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `segnalazione`
--
ALTER TABLE `segnalazione`
  MODIFY `id_segnalazione` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `utente_registrato`
--
ALTER TABLE `utente_registrato`
  MODIFY `id_utente` int(11) NOT NULL AUTO_INCREMENT;

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
