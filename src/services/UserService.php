<?php

require_once __DIR__ . '/BaseService.php';

class UserService extends BaseService
{
    public function findById(int $idUtente): ?array
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $stmt = $this->db->prepare("
            SELECT u.id_utente, u.email, u.username, u.nome, u.telefono,
                   u.propic, u.stato_ban, u.data_registrazione,
                   i.via, i.numero, i.cap, i.citta, i.provincia, i.paese
            FROM utente_registrato u
            LEFT JOIN indirizzi i ON i.id_utente = u.id_utente AND i.predefinito = 1
            WHERE u.id_utente = ?
            LIMIT 1
        ");
        $stmt->execute([$idUtente]);

        return $stmt->fetch() ?: null;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT id_utente, email, username, nome, telefono, stato_ban, data_registrazione
            FROM utente_registrato
            ORDER BY data_registrazione DESC
        ");

        return $stmt->fetchAll();
    }

    public function banna(int $idUtente): void
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $stmt = $this->db->prepare("
            UPDATE utente_registrato
            SET stato_ban = 1
            WHERE id_utente = ?
        ");
        $stmt->execute([$idUtente]);
    }

    public function sblocca(int $idUtente): void
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $stmt = $this->db->prepare("
            UPDATE utente_registrato
            SET stato_ban = 0
            WHERE id_utente = ?
        ");
        $stmt->execute([$idUtente]);
    }

    public function updateIndirizzoSpedizione(int $idUtente, array $data): void
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $nome      = $this->clean($data['nome']      ?? '');
        $via       = $this->clean($data['via']       ?? '');
        $numero    = $this->clean($data['numero']    ?? '');
        $cap       = $this->clean($data['cap']       ?? '');
        $citta     = $this->clean($data['citta']     ?? '');
        $provincia = $this->clean($data['provincia'] ?? '');
        $paese     = $this->clean($data['paese']     ?? 'Italia');

        if ($via === '' || $citta === '') {
            throw new ServiceException('Via e città sono obbligatori.');
        }

        // Aggiorna il nome sull'utente se fornito
        if ($nome !== '') {
            $stmt = $this->db->prepare("
                UPDATE utente_registrato
                SET nome = ?
                WHERE id_utente = ?
            ");
            $stmt->execute([$nome, $idUtente]);
        }

        // Upsert indirizzo: aggiorna se esiste già uno predefinito, altrimenti inserisce
        $stmt = $this->db->prepare("
            INSERT INTO indirizzi
                (id_utente, tipo, via, numero, cap, citta, provincia, paese, predefinito)
            VALUES (?, 'spedizione', ?, ?, ?, ?, ?, ?, 1)
            ON DUPLICATE KEY UPDATE
                via       = VALUES(via),
                numero    = VALUES(numero),
                cap       = VALUES(cap),
                citta     = VALUES(citta),
                provincia = VALUES(provincia),
                paese     = VALUES(paese)
        ");
        $stmt->execute([
            $idUtente,
            $via,
            $numero    !== '' ? $numero    : null,
            $cap       !== '' ? $cap       : null,
            $citta,
            $provincia !== '' ? $provincia : null,
            $paese,
        ]);
    }
}