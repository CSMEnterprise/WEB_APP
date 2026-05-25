<?php

namespace App\Services;

use App\Entity\EAccountBusiness;
use App\Entity\EAdmin;
use App\Entity\EAnnuncio;
use App\Entity\ECarrello;
use App\Entity\ECategoria;
use App\Entity\EElementoCarrello;
use App\Entity\EFeedback;
use App\Entity\EImmagine;
use App\Entity\EIndirizzo;
use App\Entity\EModera;
use App\Entity\EPagamento;
use App\Entity\EPreferito;
use App\Entity\ESegnalazione;
use Exception;
use PDO;
use PDOException;
use Throwable;
use finfo;

class UserService extends BaseService
{
    public function findEntityById(int $idUtente): ?EUtenteRegistrato
    {
        $utente = $this->findById($idUtente);

        return $utente ? $this->toUtenteEntity($utente) : null;
    }

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

    public function aggiornaProfiloDaEntity(EUtenteRegistrato $utente): void
    {
        $idUtente = $utente->getIdUtente();

        if ($idUtente === null) {
            throw new ServiceException('Utente non valido.');
        }

        $this->aggiornaProfiloUtente($idUtente, $utente->toArray());
    }

    public function aggiornaProfiloUtente(int $idUtente, array $data): void
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $nome     = $this->clean($data['nome']     ?? '');
        $telefono = $this->clean($data['telefono'] ?? '');

        if ($nome === '') {
            throw new ServiceException('Il nome non può essere vuoto.');
        }

        if ($telefono !== '' && !preg_match('/^\+?[0-9 ]{8,15}$/', $telefono)) {
            throw new ServiceException('Il telefono deve contenere 8-15 cifre e può iniziare con +.');
        }

        $stmt = $this->db->prepare("
            UPDATE utente_registrato
            SET nome = ?, telefono = ?
            WHERE id_utente = ?
        ");
        $stmt->execute([
            $nome,
            $telefono !== '' ? $telefono : null,
            $idUtente,
        ]);
    }

    public function search(string $q): array
    {
        $q = $this->clean($q);

        if ($q === '') {
            return [];
        }

        $stmt = $this->db->prepare("
            SELECT id_utente, username, nome, propic, data_registrazione
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

    public function searchEntity(string $q): array
    {
        return $this->toUtenteEntities($this->search($q));
    }

    public function getAll(string $q = ''): array
    {
        $q = $this->clean($q);

        $where = '';
        $params = [];

        if ($q !== '') {
            $where = "
                WHERE id_utente = ?
                   OR email LIKE CONCAT('%', ?, '%')
                   OR username LIKE CONCAT('%', ?, '%')
                   OR nome LIKE CONCAT('%', ?, '%')
                   OR telefono LIKE CONCAT('%', ?, '%')
            ";
            $id = ctype_digit($q) ? (int) $q : 0;
            $params = [$id, $q, $q, $q, $q];
        }

        $stmt = $this->db->prepare("
            SELECT id_utente, email, username, nome, telefono, stato_ban, data_registrazione
            FROM utente_registrato
            {$where}
            ORDER BY data_registrazione DESC
        ");
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function getAllEntity(string $q = ''): array
    {
        return $this->toUtenteEntities($this->getAll($q));
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

    public function updateIndirizzoSpedizioneDaEntity(EIndirizzo $indirizzo): void
    {
        $idUtente = $indirizzo->getIdUtente();

        if ($idUtente === null) {
            throw new ServiceException('Utente non valido.');
        }

        $this->updateIndirizzoSpedizione($idUtente, $indirizzo->toArray());
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
            SELECT COUNT(*)
            FROM indirizzi
            WHERE id_utente = ?
        ");
        $stmt->execute([$idUtente]);
        $predefinito = ((int) $stmt->fetchColumn()) === 0 ? 1 : 0;

        $stmt = $this->db->prepare("
            INSERT INTO indirizzi
                (id_utente, via, numero, cap, citta, provincia, paese, predefinito)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $idUtente,
            $via,
            $numero    !== '' ? $numero    : null,
            $cap       !== '' ? $cap       : null,
            $citta,
            $provincia !== '' ? $provincia : null,
            $paese,
            $predefinito,
        ]);
    }

    public function getIndirizziByUserId(int $idUtente): array
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $stmt = $this->db->prepare("
            SELECT *
            FROM indirizzi
            WHERE id_utente = ?
            ORDER BY predefinito DESC, id_indirizzo DESC
        ");
        $stmt->execute([$idUtente]);

        return $stmt->fetchAll();
    }

    public function getIndirizziByUserIdEntity(int $idUtente): array
    {
        return $this->toIndirizzoEntities($this->getIndirizziByUserId($idUtente));
    }

    public function findIndirizzoByIdForUser(int $idIndirizzo, int $idUtente): ?array
    {
        $this->requirePositiveId($idIndirizzo, 'Indirizzo');
        $this->requirePositiveId($idUtente, 'Utente');

        $stmt = $this->db->prepare("
            SELECT *
            FROM indirizzi
            WHERE id_indirizzo = ? AND id_utente = ?
            LIMIT 1
        ");
        $stmt->execute([$idIndirizzo, $idUtente]);

        return $stmt->fetch() ?: null;
    }

    public function findIndirizzoEntityByIdForUser(int $idIndirizzo, int $idUtente): ?EIndirizzo
    {
        $indirizzo = $this->findIndirizzoByIdForUser($idIndirizzo, $idUtente);

        return $indirizzo ? $this->toIndirizzoEntity($indirizzo) : null;
    }

    public function setIndirizzoPredefinito(int $idUtente, int $idIndirizzo): void
    {
        $this->requirePositiveId($idUtente, 'Utente');
        $this->requirePositiveId($idIndirizzo, 'Indirizzo');

        if (!$this->findIndirizzoEntityByIdForUser($idIndirizzo, $idUtente)) {
            throw new ServiceException('Indirizzo non valido.');
        }

        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare("
                UPDATE indirizzi
                SET predefinito = 0
                WHERE id_utente = ?
            ");
            $stmt->execute([$idUtente]);

            $stmt = $this->db->prepare("
                UPDATE indirizzi
                SET predefinito = 1
                WHERE id_indirizzo = ? AND id_utente = ?
            ");
            $stmt->execute([$idIndirizzo, $idUtente]);

            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw new ServiceException('Impossibile impostare l indirizzo predefinito.');
        }
    }

    public function modificaIndirizzo(int $idIndirizzo, int $idUtente, array $data): void
    {
        $this->requirePositiveId($idIndirizzo, 'Indirizzo');
        $this->requirePositiveId($idUtente, 'Utente');

        if (!$this->findIndirizzoEntityByIdForUser($idIndirizzo, $idUtente)) {
            throw new ServiceException('Indirizzo non trovato.');
        }

        $via       = $this->clean($data['via']       ?? '');
        $numero    = $this->clean($data['numero']    ?? '');
        $cap       = $this->clean($data['cap']       ?? '');
        $citta     = $this->clean($data['citta']     ?? '');
        $provincia = $this->clean($data['provincia'] ?? '');
        $paese     = $this->clean($data['paese']     ?? 'Italia');

        if ($via === '' || $citta === '') {
            throw new ServiceException('Via e città sono obbligatori.');
        }

        $stmt = $this->db->prepare("
            UPDATE indirizzi
            SET via = ?, numero = ?, cap = ?, citta = ?, provincia = ?, paese = ?
            WHERE id_indirizzo = ? AND id_utente = ?
        ");
        $stmt->execute([
            $via,
            $numero    !== '' ? $numero    : null,
            $cap       !== '' ? $cap       : null,
            $citta,
            $provincia !== '' ? $provincia : null,
            $paese,
            $idIndirizzo,
            $idUtente,
        ]);
    }

    public function modificaIndirizzoDaEntity(EIndirizzo $indirizzo): void
    {
        $idIndirizzo = $indirizzo->getIdIndirizzo();
        $idUtente = $indirizzo->getIdUtente();

        if ($idIndirizzo === null || $idUtente === null) {
            throw new ServiceException('Indirizzo non valido.');
        }

        $this->modificaIndirizzo($idIndirizzo, $idUtente, $indirizzo->toArray());
    }

    public function eliminaIndirizzo(int $idIndirizzo, int $idUtente): void
    {
        $this->requirePositiveId($idIndirizzo, 'Indirizzo');
        $this->requirePositiveId($idUtente, 'Utente');

        $indirizzo = $this->findIndirizzoEntityByIdForUser($idIndirizzo, $idUtente);
        if (!$indirizzo) {
            throw new ServiceException('Indirizzo non trovato.');
        }

        $stmt = $this->db->prepare("DELETE FROM indirizzi WHERE id_indirizzo = ? AND id_utente = ?");
        $stmt->execute([$idIndirizzo, $idUtente]);

        // Se era predefinito, imposta come predefinito il più recente rimasto
        if ($indirizzo->isPredefinito()) {
            $stmt = $this->db->prepare("
                UPDATE indirizzi SET predefinito = 1
                WHERE id_utente = ?
                ORDER BY id_indirizzo DESC
                LIMIT 1
            ");
            $stmt->execute([$idUtente]);
        }
    }

    private function toUtenteEntity(array $utente): EUtenteRegistrato
    {
        return EUtenteRegistrato::fromArray($utente);
    }

    private function toUtenteEntities(array $utenti): array
    {
        return array_map(fn(array $utente) => $this->toUtenteEntity($utente), $utenti);
    }

    private function toIndirizzoEntity(array $indirizzo): EIndirizzo
    {
        return EIndirizzo::fromArray($indirizzo);
    }

    private function toIndirizzoEntities(array $indirizzi): array
    {
        return array_map(fn(array $indirizzo) => $this->toIndirizzoEntity($indirizzo), $indirizzi);
    }
}
