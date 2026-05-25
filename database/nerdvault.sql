-- NerdVault - schema database completo
-- File pulito per creare il database senza modifiche successive alle tabelle.
-- Importare questo file su un database vuoto.
-- Se il database contiene gia dati importanti, fare prima un backup.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `nerdvault`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `nerdvault`;

-- --------------------------------------------------------
-- Tabella `admin`
-- --------------------------------------------------------

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `livello_sicurezza` int(11) NOT NULL DEFAULT 1,
  `stato_ban` tinyint(1) NOT NULL DEFAULT 0,
  `data_creazione` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_admin`),
  UNIQUE KEY `email` (`email`),
  CONSTRAINT `chk_admin_livello_sicurezza`
    CHECK (`livello_sicurezza` >= 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabella `utente_registrato`
-- --------------------------------------------------------

CREATE TABLE `utente_registrato` (
  `id_utente` int(11) NOT NULL AUTO_INCREMENT,
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
  `data_registrazione` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_utente`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabella `categoria`
-- --------------------------------------------------------

CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `id_padre` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_categoria`),
  UNIQUE KEY `nome` (`nome`),
  KEY `id_padre` (`id_padre`),
  CONSTRAINT `categoria_ibfk_1`
    FOREIGN KEY (`id_padre`) REFERENCES `categoria` (`id_categoria`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=58;

-- Dati di base per `categoria`

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
-- Tabella `account_business`
-- --------------------------------------------------------

CREATE TABLE `account_business` (
  `id_acc_business` int(11) NOT NULL AUTO_INCREMENT,
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
  `data_verifica` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_acc_business`),
  UNIQUE KEY `id_utente` (`id_utente`),
  UNIQUE KEY `p_iva` (`p_iva`),
  UNIQUE KEY `email_aziendale` (`email_aziendale`),
  KEY `id_admin_verifica` (`id_admin_verifica`),
  CONSTRAINT `account_business_ibfk_1`
    FOREIGN KEY (`id_utente`) REFERENCES `utente_registrato` (`id_utente`)
    ON DELETE CASCADE,
  CONSTRAINT `account_business_ibfk_2`
    FOREIGN KEY (`id_admin_verifica`) REFERENCES `admin` (`id_admin`)
    ON DELETE SET NULL,
  CONSTRAINT `chk_account_business_verificato`
    CHECK (`verificato` IN (0, 1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabella `indirizzi`
-- --------------------------------------------------------

CREATE TABLE `indirizzi` (
  `id_indirizzo` int(11) NOT NULL AUTO_INCREMENT,
  `id_utente` int(11) DEFAULT NULL,
  `id_business` int(11) DEFAULT NULL,
  `tipo` enum('casa','lavoro','fatturazione','spedizione') DEFAULT 'casa',
  `via` varchar(100) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `cap` char(5) DEFAULT NULL,
  `citta` varchar(100) DEFAULT NULL,
  `provincia` char(2) DEFAULT NULL,
  `paese` varchar(50) NOT NULL DEFAULT 'Italia',
  `predefinito` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_indirizzo`),
  KEY `id_utente` (`id_utente`),
  KEY `id_business` (`id_business`),
  CONSTRAINT `indirizzi_ibfk_1`
    FOREIGN KEY (`id_utente`) REFERENCES `utente_registrato` (`id_utente`)
    ON DELETE CASCADE,
  CONSTRAINT `indirizzi_ibfk_2`
    FOREIGN KEY (`id_business`) REFERENCES `account_business` (`id_acc_business`)
    ON DELETE CASCADE,
  CONSTRAINT `chk_indirizzi_proprietario`
    CHECK (
      (`id_utente` IS NOT NULL AND `id_business` IS NULL)
      OR
      (`id_utente` IS NULL AND `id_business` IS NOT NULL)
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabella `annuncio`
-- --------------------------------------------------------

CREATE TABLE `annuncio` (
  `id_annuncio` int(11) NOT NULL AUTO_INCREMENT,
  `id_utente` int(11) DEFAULT NULL,
  `id_business` int(11) DEFAULT NULL,
  `id_categoria` int(11) NOT NULL,
  `titolo` varchar(255) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `stato_conservazione` enum('Nuovo','Usato come nuovo','Ottimo','Buono','Discreto','Scarso') NOT NULL,
  `prezzo` decimal(10,2) NOT NULL,
  `modalita_consegna` enum('Consegna') NOT NULL,
  `stato` enum('attivo','venduto') NOT NULL DEFAULT 'attivo',
  `data_creazione` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_scadenza` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_annuncio`),
  KEY `id_utente` (`id_utente`),
  KEY `id_business` (`id_business`),
  KEY `id_categoria` (`id_categoria`),
  CONSTRAINT `annuncio_ibfk_1`
    FOREIGN KEY (`id_utente`) REFERENCES `utente_registrato` (`id_utente`)
    ON DELETE CASCADE,
  CONSTRAINT `annuncio_ibfk_2`
    FOREIGN KEY (`id_business`) REFERENCES `account_business` (`id_acc_business`)
    ON DELETE CASCADE,
  CONSTRAINT `annuncio_ibfk_3`
    FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`),
  CONSTRAINT `chk_annuncio_prezzo`
    CHECK (`prezzo` >= 0),
  CONSTRAINT `chk_annuncio_venditore`
    CHECK (
      (`id_utente` IS NOT NULL AND `id_business` IS NULL)
      OR
      (`id_utente` IS NULL AND `id_business` IS NOT NULL)
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabella `immagine`
-- --------------------------------------------------------

CREATE TABLE `immagine` (
  `id_immagine` int(11) NOT NULL AUTO_INCREMENT,
  `id_annuncio` int(11) NOT NULL,
  `url` varchar(500) NOT NULL,
  `ordine` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_immagine`),
  UNIQUE KEY `id_annuncio` (`id_annuncio`,`ordine`),
  CONSTRAINT `immagine_ibfk_1`
    FOREIGN KEY (`id_annuncio`) REFERENCES `annuncio` (`id_annuncio`)
    ON DELETE CASCADE,
  CONSTRAINT `chk_immagine_ordine`
    CHECK (`ordine` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabella `carrello`
-- --------------------------------------------------------

CREATE TABLE `carrello` (
  `id_carrello` int(11) NOT NULL AUTO_INCREMENT,
  `id_utente` int(11) NOT NULL,
  `data_creazione` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_carrello`),
  UNIQUE KEY `id_utente` (`id_utente`),
  CONSTRAINT `carrello_ibfk_1`
    FOREIGN KEY (`id_utente`) REFERENCES `utente_registrato` (`id_utente`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabella `elemento_carrello`
-- --------------------------------------------------------

CREATE TABLE `elemento_carrello` (
  `id_elemento_carrello` int(11) NOT NULL AUTO_INCREMENT,
  `id_carrello` int(11) NOT NULL,
  `id_annuncio` int(11) NOT NULL,
  `data_aggiunta` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_elemento_carrello`),
  UNIQUE KEY `id_carrello` (`id_carrello`,`id_annuncio`),
  KEY `id_annuncio` (`id_annuncio`),
  CONSTRAINT `elemento_carrello_ibfk_1`
    FOREIGN KEY (`id_carrello`) REFERENCES `carrello` (`id_carrello`)
    ON DELETE CASCADE,
  CONSTRAINT `elemento_carrello_ibfk_2`
    FOREIGN KEY (`id_annuncio`) REFERENCES `annuncio` (`id_annuncio`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabella `pagamento`
-- --------------------------------------------------------

CREATE TABLE `pagamento` (
  `id_pagamento` int(11) NOT NULL AUTO_INCREMENT,
  `id_annuncio` int(11) NOT NULL,
  `id_acquirente` int(11) NOT NULL,
  `id_indirizzo_spedizione` int(11) NOT NULL,
  `importo_totale` decimal(10,2) NOT NULL,
  `stato` enum('In_attesa','Completato','Annullato','Rimborsato') NOT NULL DEFAULT 'In_attesa',
  `paypal_transaction_id` varchar(255) DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_pagamento`),
  UNIQUE KEY `paypal_transaction_id` (`paypal_transaction_id`),
  KEY `id_annuncio` (`id_annuncio`),
  KEY `id_acquirente` (`id_acquirente`),
  KEY `id_indirizzo_spedizione` (`id_indirizzo_spedizione`),
  CONSTRAINT `pagamento_ibfk_1`
    FOREIGN KEY (`id_annuncio`) REFERENCES `annuncio` (`id_annuncio`),
  CONSTRAINT `pagamento_ibfk_2`
    FOREIGN KEY (`id_acquirente`) REFERENCES `utente_registrato` (`id_utente`),
  CONSTRAINT `pagamento_ibfk_3`
    FOREIGN KEY (`id_indirizzo_spedizione`) REFERENCES `indirizzi` (`id_indirizzo`),
  CONSTRAINT `chk_pagamento_importo`
    CHECK (`importo_totale` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabella `feedback`
-- --------------------------------------------------------

CREATE TABLE `feedback` (
  `id_feedback` int(11) NOT NULL AUTO_INCREMENT,
  `id_autore` int(11) NOT NULL,
  `id_pagamento` int(11) NOT NULL,
  `valutazione` tinyint(4) NOT NULL,
  `commento` text DEFAULT NULL,
  `data_feedback` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_feedback`),
  UNIQUE KEY `id_pagamento` (`id_pagamento`),
  KEY `id_autore` (`id_autore`),
  CONSTRAINT `feedback_ibfk_1`
    FOREIGN KEY (`id_autore`) REFERENCES `utente_registrato` (`id_utente`)
    ON DELETE CASCADE,
  CONSTRAINT `feedback_ibfk_2`
    FOREIGN KEY (`id_pagamento`) REFERENCES `pagamento` (`id_pagamento`)
    ON DELETE CASCADE,
  CONSTRAINT `chk_feedback_valutazione`
    CHECK (`valutazione` BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabella `password_reset`
-- --------------------------------------------------------

CREATE TABLE `password_reset` (
  `id_reset` int(11) NOT NULL AUTO_INCREMENT,
  `id_utente` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `scadenza` datetime NOT NULL,
  `usato` tinyint(1) NOT NULL DEFAULT 0,
  `creato_il` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_reset`),
  UNIQUE KEY `token` (`token`),
  KEY `id_utente` (`id_utente`),
  CONSTRAINT `password_reset_ibfk_1`
    FOREIGN KEY (`id_utente`) REFERENCES `utente_registrato` (`id_utente`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabella `preferito`
-- --------------------------------------------------------

CREATE TABLE `preferito` (
  `id_utente` int(11) NOT NULL,
  `id_annuncio` int(11) NOT NULL,
  `data_aggiunta` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_utente`,`id_annuncio`),
  KEY `id_annuncio` (`id_annuncio`),
  CONSTRAINT `preferito_ibfk_1`
    FOREIGN KEY (`id_utente`) REFERENCES `utente_registrato` (`id_utente`)
    ON DELETE CASCADE,
  CONSTRAINT `preferito_ibfk_2`
    FOREIGN KEY (`id_annuncio`) REFERENCES `annuncio` (`id_annuncio`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabella `segnalazione`
-- --------------------------------------------------------

CREATE TABLE `segnalazione` (
  `id_segnalazione` int(11) NOT NULL AUTO_INCREMENT,
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
  `data_risoluzione` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_segnalazione`),
  KEY `id_segnalante` (`id_segnalante`),
  KEY `id_utente_segnalato` (`id_utente_segnalato`),
  KEY `id_admin` (`id_admin`),
  KEY `id_feedback` (`id_feedback`),
  KEY `id_annuncio` (`id_annuncio`),
  KEY `id_business` (`id_business`),
  CONSTRAINT `segnalazione_ibfk_1`
    FOREIGN KEY (`id_segnalante`) REFERENCES `utente_registrato` (`id_utente`)
    ON DELETE CASCADE,
  CONSTRAINT `segnalazione_ibfk_2`
    FOREIGN KEY (`id_utente_segnalato`) REFERENCES `utente_registrato` (`id_utente`)
    ON DELETE CASCADE,
  CONSTRAINT `segnalazione_ibfk_3`
    FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`)
    ON DELETE SET NULL,
  CONSTRAINT `segnalazione_ibfk_4`
    FOREIGN KEY (`id_feedback`) REFERENCES `feedback` (`id_feedback`)
    ON DELETE CASCADE,
  CONSTRAINT `segnalazione_ibfk_5`
    FOREIGN KEY (`id_annuncio`) REFERENCES `annuncio` (`id_annuncio`)
    ON DELETE CASCADE,
  CONSTRAINT `segnalazione_ibfk_6`
    FOREIGN KEY (`id_business`) REFERENCES `account_business` (`id_acc_business`)
    ON DELETE CASCADE,
  CONSTRAINT `chk_segnalazione_oggetto`
    CHECK (
      (`id_annuncio` IS NOT NULL AND `id_utente_segnalato` IS NULL AND `id_business` IS NULL AND `id_feedback` IS NULL)
      OR
      (`id_annuncio` IS NULL AND `id_utente_segnalato` IS NOT NULL AND `id_business` IS NULL AND `id_feedback` IS NULL)
      OR
      (`id_annuncio` IS NULL AND `id_utente_segnalato` IS NULL AND `id_business` IS NOT NULL AND `id_feedback` IS NULL)
      OR
      (`id_annuncio` IS NULL AND `id_utente_segnalato` IS NULL AND `id_business` IS NULL AND `id_feedback` IS NOT NULL)
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabella `modera`
-- --------------------------------------------------------

CREATE TABLE `modera` (
  `id_moderazione` int(11) NOT NULL AUTO_INCREMENT,
  `id_admin` int(11) NOT NULL,
  `id_utente` int(11) DEFAULT NULL,
  `id_feedback` int(11) DEFAULT NULL,
  `id_annuncio` int(11) DEFAULT NULL,
  `id_business` int(11) DEFAULT NULL,
  `azione_compiuta` text NOT NULL,
  `data_azione` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_moderazione`),
  KEY `id_admin` (`id_admin`),
  KEY `id_utente` (`id_utente`),
  KEY `id_feedback` (`id_feedback`),
  KEY `id_annuncio` (`id_annuncio`),
  KEY `id_business` (`id_business`),
  CONSTRAINT `modera_ibfk_1`
    FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`),
  CONSTRAINT `modera_ibfk_2`
    FOREIGN KEY (`id_utente`) REFERENCES `utente_registrato` (`id_utente`)
    ON DELETE CASCADE,
  CONSTRAINT `modera_ibfk_3`
    FOREIGN KEY (`id_feedback`) REFERENCES `feedback` (`id_feedback`)
    ON DELETE CASCADE,
  CONSTRAINT `modera_ibfk_4`
    FOREIGN KEY (`id_annuncio`) REFERENCES `annuncio` (`id_annuncio`)
    ON DELETE CASCADE,
  CONSTRAINT `modera_ibfk_5`
    FOREIGN KEY (`id_business`) REFERENCES `account_business` (`id_acc_business`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
