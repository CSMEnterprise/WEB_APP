ALTER TABLE admin
ADD COLUMN stato_ban TINYINT(1) NOT NULL DEFAULT 0
AFTER livello_sicurezza;
