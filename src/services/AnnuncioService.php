<?php

require_once __DIR__ . '/BaseService.php';

class AnnuncioService extends BaseService
{
    public function getAnnunciAttivi(): array
    {
        $stmt = $this->db->query("
            SELECT
                a.*,
                c.nome AS categoria_nome,
                u.username AS venditore_username,
                (
                    SELECT i.url
                    FROM immagine i
                    WHERE i.id_annuncio = a.id_annuncio
                    ORDER BY i.ordine ASC, i.id_immagine ASC
                    LIMIT 1
                ) AS immagine_principale
            FROM annuncio a
            LEFT JOIN categoria c ON c.id_categoria = a.id_categoria
            LEFT JOIN utente_registrato u ON u.id_utente = a.id_utente
            WHERE a.stato = 'attivo'
            ORDER BY a.data_creazione DESC
        ");

        return $stmt->fetchAll();
    }

    public function findById(int $idAnnuncio): ?array
    {
        $this->requirePositiveId($idAnnuncio, 'Annuncio');

        $stmt = $this->db->prepare("
            SELECT a.*, c.nome AS categoria_nome, u.username AS venditore_username
            FROM annuncio a
            LEFT JOIN categoria c ON c.id_categoria = a.id_categoria
            LEFT JOIN utente_registrato u ON u.id_utente = a.id_utente
            WHERE a.id_annuncio = ?
            LIMIT 1
        ");
        $stmt->execute([$idAnnuncio]);

        $annuncio = $stmt->fetch() ?: null;

        if (!$annuncio) {
            return null;
        }

        $annuncio['immagini'] = $this->getImmaginiByAnnuncio($idAnnuncio);

        return $annuncio;
    }

    public function getByUserId(int $idUtente): array
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $stmt = $this->db->prepare("
            SELECT
                a.*,
                c.nome AS categoria_nome,
                u.username AS venditore_username,
                (
                    SELECT i.url
                    FROM immagine i
                    WHERE i.id_annuncio = a.id_annuncio
                    ORDER BY i.ordine ASC, i.id_immagine ASC
                    LIMIT 1
                ) AS immagine_principale
            FROM annuncio a
            LEFT JOIN categoria c ON c.id_categoria = a.id_categoria
            LEFT JOIN utente_registrato u ON u.id_utente = a.id_utente
            WHERE a.id_utente = ?
            ORDER BY a.data_creazione DESC
        ");
        $stmt->execute([$idUtente]);

        return $stmt->fetchAll();
    }

    public function crea(array $data, int $idUtente, array $files = []): int
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $titolo = $this->clean($data['titolo'] ?? '');
        $descrizione = $this->clean($data['descrizione'] ?? '');
        $idCategoria = (int) ($data['id_categoria'] ?? 0);
        $statoConservazione = $this->clean($data['stato_conservazione'] ?? '');
        $prezzo = (float) ($data['prezzo'] ?? 0);

        if ($titolo === '' || $idCategoria <= 0 || $statoConservazione === '' || $prezzo <= 0) {
            throw new ServiceException('Compila tutti i campi obbligatori dell’annuncio.');
        }

        try {
            $this->db->beginTransaction();

            // La colonna modalita_consegna esiste ancora nel dump SQL ed è NOT NULL.
            // La nascondiamo dal form e salviamo un valore fisso per mantenere compatibilità con il DB attuale.
            $modalitaConsegnaDefault = 'Consegna';

            $stmt = $this->db->prepare("
                INSERT INTO annuncio
                (id_utente, id_categoria, titolo, descrizione, stato_conservazione, prezzo, modalita_consegna, stato)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'attivo')
            ");

            $stmt->execute([
                $idUtente,
                $idCategoria,
                $titolo,
                $descrizione !== '' ? $descrizione : null,
                $statoConservazione,
                $prezzo,
                $modalitaConsegnaDefault
            ]);

            $idAnnuncio = $this->lastInsertId();
            $this->salvaImmaginiAnnuncio($idAnnuncio, $files);

            $this->db->commit();

            return $idAnnuncio;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function elimina(int $idAnnuncio, int $idUtente): void
    {
        $this->requirePositiveId($idAnnuncio, 'Annuncio');
        $this->requirePositiveId($idUtente, 'Utente');

        $stmt = $this->db->prepare("
            DELETE FROM annuncio
            WHERE id_annuncio = ? AND id_utente = ?
        ");
        $stmt->execute([$idAnnuncio, $idUtente]);

        if ($stmt->rowCount() === 0) {
            throw new ServiceException('Non puoi eliminare questo annuncio.');
        }
    }

    public function eliminaDaAdmin(int $idAnnuncio): void
    {
        $this->requirePositiveId($idAnnuncio, 'Annuncio');

        $stmt = $this->db->prepare("
            DELETE FROM annuncio
            WHERE id_annuncio = ?
        ");
        $stmt->execute([$idAnnuncio]);
    }

    public function marcaVenduto(int $idAnnuncio): void
    {
        $this->requirePositiveId($idAnnuncio, 'Annuncio');

        $stmt = $this->db->prepare("
            UPDATE annuncio
            SET stato = 'venduto'
            WHERE id_annuncio = ?
        ");
        $stmt->execute([$idAnnuncio]);
    }

    public function searchAnnunci(string $keywords): array
    {
        $keywords = $this->clean($keywords);

        if ($keywords === '') {
            return $this->getAnnunciAttivi();
        }

        $stmt = $this->db->prepare("
            SELECT
                a.*,
                c.nome AS categoria_nome,
                u.username AS venditore_username,
                (
                    SELECT i.url
                    FROM immagine i
                    WHERE i.id_annuncio = a.id_annuncio
                    ORDER BY i.ordine ASC, i.id_immagine ASC
                    LIMIT 1
                ) AS immagine_principale
            FROM annuncio a
            LEFT JOIN categoria c ON c.id_categoria = a.id_categoria
            LEFT JOIN utente_registrato u ON u.id_utente = a.id_utente
            WHERE a.stato = 'attivo'
            AND (
                a.titolo LIKE CONCAT('%', ?, '%')
                OR a.descrizione LIKE CONCAT('%', ?, '%')
            )
            ORDER BY a.data_creazione DESC
        ");

        $stmt->execute([$keywords, $keywords]);

        return $stmt->fetchAll();
    }

    private function getImmaginiByAnnuncio(int $idAnnuncio): array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM immagine
            WHERE id_annuncio = ?
            ORDER BY ordine ASC, id_immagine ASC
        ");
        $stmt->execute([$idAnnuncio]);

        return $stmt->fetchAll();
    }

    private function salvaImmaginiAnnuncio(int $idAnnuncio, array $files): void
    {
        if (empty($files['immagini']) || empty($files['immagini']['name'])) {
            return;
        }

        $immagini = $files['immagini'];
        $nomi = is_array($immagini['name']) ? $immagini['name'] : [$immagini['name']];
        $tmpNames = is_array($immagini['tmp_name']) ? $immagini['tmp_name'] : [$immagini['tmp_name']];
        $errori = is_array($immagini['error']) ? $immagini['error'] : [$immagini['error']];
        $dimensioni = is_array($immagini['size']) ? $immagini['size'] : [$immagini['size']];

        $maxFile = 5;
        $maxSize = 3 * 1024 * 1024; // 3 MB
        $allowedMime = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        $uploadDir = __DIR__ . '/../../public/uploads/annunci/' . $idAnnuncio;
        $publicDir = 'uploads/annunci/' . $idAnnuncio;

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true)) {
            throw new ServiceException('Impossibile creare la cartella per le immagini.');
        }

        $stmt = $this->db->prepare("
            INSERT INTO immagine (id_annuncio, url, ordine)
            VALUES (?, ?, ?)
        ");

        $ordine = 0;
        $finfo = new finfo(FILEINFO_MIME_TYPE);

        foreach ($nomi as $index => $nomeOriginale) {
            if ($ordine >= $maxFile) {
                break;
            }

            $errore = $errori[$index] ?? UPLOAD_ERR_NO_FILE;

            if ($errore === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if ($errore !== UPLOAD_ERR_OK) {
                throw new ServiceException('Errore durante il caricamento di una foto.');
            }

            $tmpName = $tmpNames[$index] ?? '';
            $size = (int) ($dimensioni[$index] ?? 0);

            if ($size <= 0 || $size > $maxSize) {
                throw new ServiceException('Ogni foto deve pesare al massimo 3 MB.');
            }

            $mime = $finfo->file($tmpName);

            if (!isset($allowedMime[$mime])) {
                throw new ServiceException('Puoi caricare solo immagini JPG, PNG o WEBP.');
            }

            $extension = $allowedMime[$mime];
            $filename = bin2hex(random_bytes(16)) . '.' . $extension;
            $destinazione = $uploadDir . '/' . $filename;

            if (!move_uploaded_file($tmpName, $destinazione)) {
                throw new ServiceException('Impossibile salvare una foto caricata.');
            }

            $url = $publicDir . '/' . $filename;
            $stmt->execute([$idAnnuncio, $url, $ordine]);
            $ordine++;
        }
    }
}
