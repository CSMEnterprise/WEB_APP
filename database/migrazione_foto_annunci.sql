-- Se la tabella immagine è già presente nel tuo database, non serve eseguire nulla.
-- Nel dump repomix la tabella `immagine` esiste già con: id_immagine, id_annuncio, url, ordine.

-- Facoltativo: se in futuro vuoi rimuovere davvero la colonna dal database:
-- ALTER TABLE annuncio DROP COLUMN modalita_consegna;
-- In quel caso aggiorna anche AnnuncioService.php togliendo modalita_consegna dall'INSERT.
