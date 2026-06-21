-- NerdVault - dati dimostrativi per esame
-- Importare DOPO database/nerdvault.sql.
--
-- Tutti gli account demo usano la password: Demo2026!
-- Gli ID 9000-9999 sono riservati a questo dataset, che puo essere
-- reimportato senza cancellare eventuali dati reali con ID differenti.

SET NAMES utf8mb4;
SET time_zone = '+00:00';
USE `nerdvault`;

START TRANSACTION;

-- Pulizia del solo dataset demo, in ordine compatibile con le foreign key.
DELETE FROM `modera` WHERE `id_moderazione` BETWEEN 9000 AND 9999;
DELETE FROM `segnalazione` WHERE `id_segnalazione` BETWEEN 9000 AND 9999;
DELETE FROM `feedback` WHERE `id_feedback` BETWEEN 9000 AND 9999;
DELETE FROM `preferito` WHERE `id_utente` BETWEEN 9000 AND 9999;
DELETE FROM `elemento_carrello` WHERE `id_elemento_carrello` BETWEEN 9000 AND 9999;
DELETE FROM `carrello` WHERE `id_carrello` BETWEEN 9000 AND 9999;
DELETE FROM `pagamento` WHERE `id_pagamento` BETWEEN 9000 AND 9999;
DELETE FROM `immagine` WHERE `id_immagine` BETWEEN 9000 AND 9999;
DELETE FROM `annuncio` WHERE `id_annuncio` BETWEEN 9000 AND 9999;
DELETE FROM `indirizzi` WHERE `id_indirizzo` BETWEEN 9000 AND 9999;
DELETE FROM `account_business` WHERE `id_acc_business` BETWEEN 9000 AND 9999;
DELETE FROM `utente_registrato` WHERE `id_utente` BETWEEN 9000 AND 9999;
DELETE FROM `admin` WHERE `id_admin` BETWEEN 9000 AND 9999;

-- Password comune: Demo2026!
SET @demo_password_hash = '$2y$10$7Cs.Rc2OB7kd8kZB7MnvEODLrrK/Wufnrdodfy6OdbQzN9PeUhfEi';

INSERT INTO `admin`
  (`id_admin`, `email`, `password_hash`, `livello_sicurezza`, `stato_ban`, `data_creazione`)
VALUES
  (9001, 'admin.demo@nerdvault.test', @demo_password_hash, 1, 0, DATE_SUB(NOW(), INTERVAL 180 DAY)),
  (9002, 'superadmin.demo@nerdvault.test', @demo_password_hash, 2, 0, DATE_SUB(NOW(), INTERVAL 240 DAY));

INSERT INTO `utente_registrato`
  (`id_utente`, `email`, `email_verificata`, `token_verifica`, `token_scadenza`, `username`, `password_hash`, `nome`, `telefono`, `propic`, `stato_ban`, `data_registrazione`)
VALUES
  (9101, 'alice.demo@nerdvault.test', 1, NULL, NULL, 'alice_collector', @demo_password_hash, 'Alice Bianchi', '+39 320 1111111', NULL, 0, DATE_SUB(NOW(), INTERVAL 150 DAY)),
  (9102, 'marco.demo@nerdvault.test', 1, NULL, NULL, 'marco_retro', @demo_password_hash, 'Marco Rossi', '+39 320 2222222', NULL, 0, DATE_SUB(NOW(), INTERVAL 140 DAY)),
  (9103, 'giulia.demo@nerdvault.test', 1, NULL, NULL, 'giulia_cards', @demo_password_hash, 'Giulia Verdi', '+39 320 3333333', NULL, 0, DATE_SUB(NOW(), INTERVAL 120 DAY)),
  (9104, 'business.demo@nerdvault.test', 1, NULL, NULL, 'vault_store', @demo_password_hash, 'Luca Neri', '+39 320 4444444', NULL, 0, DATE_SUB(NOW(), INTERVAL 210 DAY)),
  (9105, 'banned.demo@nerdvault.test', 1, NULL, NULL, 'utente_bannato', @demo_password_hash, 'Demo Bannato', '+39 320 5555555', NULL, 1, DATE_SUB(NOW(), INTERVAL 90 DAY)),
  (9106, 'nonverificato.demo@nerdvault.test', 0, REPEAT('a', 64), DATE_ADD(NOW(), INTERVAL 48 HOUR), 'utente_non_verificato', @demo_password_hash, 'Demo Non Verificato', '+39 320 6666666', NULL, 0, DATE_SUB(NOW(), INTERVAL 1 DAY));

INSERT INTO `account_business`
  (`id_acc_business`, `id_utente`, `p_iva`, `nome_azienda`, `logo`, `descrizione`, `telefono`, `email_aziendale`, `link_social`, `verificato`, `data_registrazione`, `id_admin_verifica`, `data_verifica`)
VALUES
  (9201, 9104, '01234567890', 'Vault Store Roma', NULL,
   'Negozio specializzato in modellismo, carte collezionabili e statue da collezione. Spedizioni tracciate in tutta Italia.',
   '+39 06 1234567', 'negozio.demo@nerdvault.test', 'https://example.com/vault-store', 1,
   DATE_SUB(NOW(), INTERVAL 200 DAY), 9002, DATE_SUB(NOW(), INTERVAL 190 DAY));

INSERT INTO `indirizzi`
  (`id_indirizzo`, `id_utente`, `id_business`, `tipo`, `via`, `numero`, `cap`, `citta`, `provincia`, `paese`, `predefinito`)
VALUES
  (9301, 9101, NULL, 'spedizione', 'Via del Collezionista', '12', '00100', 'Roma', 'RM', 'Italia', 1),
  (9302, 9101, NULL, 'lavoro', 'Viale Europa', '44', '00144', 'Roma', 'RM', 'Italia', 0),
  (9303, 9102, NULL, 'spedizione', 'Via dei Pixel', '8', '20100', 'Milano', 'MI', 'Italia', 1),
  (9304, 9103, NULL, 'spedizione', 'Corso delle Carte', '25', '10100', 'Torino', 'TO', 'Italia', 1),
  (9305, NULL, 9201, 'lavoro', 'Via del Vault', '77', '00185', 'Roma', 'RM', 'Italia', 1);

INSERT INTO `annuncio`
  (`id_annuncio`, `id_utente`, `id_business`, `id_categoria`, `titolo`, `descrizione`, `stato_conservazione`, `prezzo`, `modalita_consegna`, `stato`, `data_creazione`, `data_scadenza`)
VALUES
  (9401, 9102, NULL, 35, 'Console PlayStation 2 Slim completa', 'Console testata con controller originale, memory card e cavi. Piccoli segni sulla scocca.', 'Buono', 89.90, 'Consegna', 'attivo', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 28 DAY)),
  (9402, 9102, NULL, 12, 'Berserk Deluxe volumi 1-5', 'Prima stampa italiana, volumi letti una volta e conservati in libreria.', 'Ottimo', 145.00, 'Consegna', 'attivo', DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_ADD(NOW(), INTERVAL 24 DAY)),
  (9403, 9102, NULL, 25, 'Funko Pop Spider-Man 2099', 'Scatola integra con protector rigido incluso.', 'Usato come nuovo', 32.50, 'Consegna', 'attivo', DATE_SUB(NOW(), INTERVAL 9 DAY), DATE_ADD(NOW(), INTERVAL 21 DAY)),
  (9404, 9101, NULL, 33, 'The Legend of Zelda Collector Edition', 'Edizione da collezione completa di steelbook e artbook.', 'Ottimo', 118.00, 'Consegna', 'attivo', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_ADD(NOW(), INTERVAL 26 DAY)),
  (9405, 9101, NULL, 10, 'One Piece cofanetto East Blue', 'Cofanetto completo, nessuna pagina piegata o ingiallita.', 'Usato come nuovo', 74.90, 'Consegna', 'attivo', DATE_SUB(NOW(), INTERVAL 8 DAY), DATE_ADD(NOW(), INTERVAL 22 DAY)),
  (9406, NULL, 9201, 46, 'Gunpla RX-78-2 Master Grade', 'Kit Bandai originale sigillato, scala 1/100. Spedizione protetta.', 'Nuovo', 59.90, 'Consegna', 'attivo', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 45 DAY)),
  (9407, NULL, 9201, 38, 'Magic Commander Deck premium', 'Mazzo Commander pronto all uso con sleeves e deck box.', 'Nuovo', 79.00, 'Consegna', 'attivo', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 42 DAY)),
  (9408, NULL, 9201, 48, 'Statua Batman 1/6 numerata', 'Statua in resina numerata con certificato di autenticita e imballo originale.', 'Nuovo', 349.00, 'Consegna', 'attivo', DATE_SUB(NOW(), INTERVAL 7 DAY), DATE_ADD(NOW(), INTERVAL 38 DAY)),
  (9409, NULL, 9201, 54, 'Artbook Studio Ghibli Collection', 'Edizione cartonata illustrata, importazione ufficiale.', 'Nuovo', 44.90, 'Consegna', 'attivo', DATE_SUB(NOW(), INTERVAL 11 DAY), DATE_ADD(NOW(), INTERVAL 34 DAY)),
  (9410, 9102, NULL, 10, 'Naruto serie completa 1-72', 'Serie completa in ottime condizioni, venduta come lotto unico.', 'Ottimo', 260.00, 'Consegna', 'venduto', DATE_SUB(NOW(), INTERVAL 80 DAY), NULL),
  (9411, NULL, 9201, 36, 'Pokémon set base carta rara', 'Carta originale verificata e conservata in custodia rigida.', 'Ottimo', 129.00, 'Consegna', 'venduto', DATE_SUB(NOW(), INTERVAL 55 DAY), NULL),
  (9412, 9101, NULL, 31, 'Final Fantasy VII Remake Deluxe', 'Deluxe Edition completa di steelbook, artbook e colonna sonora.', 'Usato come nuovo', 68.00, 'Consegna', 'venduto', DATE_SUB(NOW(), INTERVAL 35 DAY), NULL);

INSERT INTO `immagine` (`id_immagine`, `id_annuncio`, `url`, `ordine`) VALUES
  (9001, 9401, '/assets/demo/gaming.svg', 0),
  (9002, 9402, '/assets/demo/manga.svg', 0),
  (9003, 9403, '/assets/demo/figure.svg', 0),
  (9004, 9404, '/assets/demo/gaming.svg', 0),
  (9005, 9405, '/assets/demo/manga.svg', 0),
  (9006, 9406, '/assets/demo/model.svg', 0),
  (9007, 9407, '/assets/demo/cards.svg', 0),
  (9008, 9408, '/assets/demo/figure.svg', 0),
  (9009, 9409, '/assets/demo/artbook.svg', 0),
  (9010, 9410, '/assets/demo/manga.svg', 0),
  (9011, 9411, '/assets/demo/cards.svg', 0),
  (9012, 9412, '/assets/demo/gaming.svg', 0);

INSERT INTO `carrello` (`id_carrello`, `id_utente`, `data_creazione`, `data_aggiornamento`) VALUES
  (9501, 9101, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 HOUR)),
  (9502, 9103, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 HOUR));

INSERT INTO `elemento_carrello`
  (`id_elemento_carrello`, `id_carrello`, `id_annuncio`, `data_aggiunta`)
VALUES
  (9511, 9501, 9406, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
  (9512, 9501, 9401, DATE_SUB(NOW(), INTERVAL 90 MINUTE)),
  (9513, 9502, 9405, DATE_SUB(NOW(), INTERVAL 1 HOUR));

INSERT INTO `preferito` (`id_utente`, `id_annuncio`, `data_aggiunta`) VALUES
  (9101, 9407, DATE_SUB(NOW(), INTERVAL 5 DAY)),
  (9101, 9408, DATE_SUB(NOW(), INTERVAL 3 DAY)),
  (9103, 9402, DATE_SUB(NOW(), INTERVAL 4 DAY)),
  (9103, 9403, DATE_SUB(NOW(), INTERVAL 2 DAY));

INSERT INTO `pagamento`
  (`id_pagamento`, `id_annuncio`, `id_acquirente`, `id_indirizzo_spedizione`, `importo_totale`, `stato`, `paypal_transaction_id`, `data`)
VALUES
  (9601, 9410, 9101, 9301, 260.00, 'Completato', 'DEMO-PAYPAL-9601', DATE_SUB(NOW(), INTERVAL 70 DAY)),
  (9602, 9411, 9103, 9304, 129.00, 'Completato', 'DEMO-PAYPAL-9602', DATE_SUB(NOW(), INTERVAL 45 DAY)),
  (9603, 9412, 9103, 9304, 68.00, 'Completato', 'DEMO-PAYPAL-9603', DATE_SUB(NOW(), INTERVAL 28 DAY));

INSERT INTO `feedback`
  (`id_feedback`, `id_autore`, `id_pagamento`, `valutazione`, `commento`, `data_feedback`)
VALUES
  (9701, 9101, 9601, 5, 'Collezione completa e imballaggio impeccabile. Venditore molto disponibile.', DATE_SUB(NOW(), INTERVAL 65 DAY)),
  (9702, 9103, 9602, 5, 'Carta conforme alla descrizione e spedizione rapida e protetta.', DATE_SUB(NOW(), INTERVAL 40 DAY)),
  (9703, 9103, 9603, 4, 'Edizione completa e ben conservata. Consegna puntuale.', DATE_SUB(NOW(), INTERVAL 24 DAY));

INSERT INTO `segnalazione`
  (`id_segnalazione`, `id_segnalante`, `id_annuncio`, `id_utente_segnalato`, `id_business`, `id_feedback`, `tipologia`, `descrizione`, `stato`, `data_segnalazione`, `id_admin`, `data_risoluzione`)
VALUES
  (9801, 9103, 9403, NULL, NULL, NULL, 'Altro', 'Verificare che la foto e la descrizione identifichino correttamente la variante.', 'Aperta', DATE_SUB(NOW(), INTERVAL 2 DAY), NULL, NULL),
  (9802, 9101, NULL, 9105, NULL, NULL, 'Spam', 'Invio ripetuto di contenuti promozionali non pertinenti.', 'Risolta', DATE_SUB(NOW(), INTERVAL 30 DAY), 9001, DATE_SUB(NOW(), INTERVAL 28 DAY)),
  (9803, 9102, NULL, NULL, NULL, 9703, 'Contenuto_inappropriato', 'Segnalazione demo in fase di valutazione da parte dello staff.', 'In_revisione', DATE_SUB(NOW(), INTERVAL 3 DAY), 9001, NULL);

INSERT INTO `modera`
  (`id_moderazione`, `id_admin`, `id_utente`, `id_feedback`, `id_annuncio`, `id_business`, `azione_compiuta`, `data_azione`)
VALUES
  (9901, 9002, NULL, NULL, NULL, 9201, 'Verifica account business Vault Store Roma', DATE_SUB(NOW(), INTERVAL 190 DAY)),
  (9902, 9001, 9105, NULL, NULL, NULL, 'Ban utente per spam ripetuto', DATE_SUB(NOW(), INTERVAL 28 DAY)),
  (9903, 9001, 9105, NULL, NULL, NULL, 'Risoluzione segnalazione #9802', DATE_SUB(NOW(), INTERVAL 28 DAY));

COMMIT;

-- Riepilogo atteso: 2 admin, 6 utenti, 1 business, 12 annunci,
-- 3 pagamenti, 3 feedback, 3 segnalazioni e 3 azioni di moderazione.
