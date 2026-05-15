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

    public function search(string $q): array
    {
        $q = $this->clean($q);

        if ($q === '') {
            return [];
        }

        $stmt = $this->db->prepare("
            SELECT id_utente, username, nome, data_registrazione
            FROM utente_registrato
            WHERE (username LIKE CONCAT('%', ?, '%')
               OR  nome     LIKE CONCAT('%', ?, '%'))
              AND stato_ban = 0
            ORDER BY username ASC
            LIMIT 20
        ");
        $stmt->execute([$q, $q]);

        return $stmt->fetchAll();
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

    public function updatePropic(int $idUtente, array $file): string
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $maxSize     = 3 * 1024 * 1024;
        $allowedMime = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new ServiceException('Errore durante il caricamento della foto.');
        }

        if (($file['size'] ?? 0) > $maxSize) {
            throw new ServiceException('La foto deve pesare al massimo 3 MB.');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);

        if (!isset($allowedMime[$mime])) {
            throw new ServiceException('Puoi caricare solo immagini JPG, PNG o WEBP.');
        }

        $ext      = $allowedMime[$mime];
        $dir      = __DIR__ . '/../../public/uploads/propic/';
        $publicDir = 'uploads/propic/';

        if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
            throw new ServiceException('Impossibile creare la cartella per le foto profilo.');
        }

        $filename = 'user_' . $idUtente . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $dest     = $dir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new ServiceException('Impossibile salvare la foto profilo.');
        }

        $url = $publicDir . $filename;

        $stmt = $this->db->prepare("UPDATE utente_registrato SET propic = ? WHERE id_utente = ?");
        $stmt->execute([$url, $idUtente]);

        return $url;
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

        $stmt = $this->db->prepare("
            DELETE FROM indirizzi
            WHERE id_utente = ? AND predefinito = 1
        ");
        $stmt->execute([$idUtente]);

        $stmt = $this->db->prepare("
            INSERT INTO indirizzi
                (id_utente, via, numero, cap, citta, provincia, paese, predefinito)
            VALUES (?, ?, ?, ?, ?, ?, ?, 1)
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