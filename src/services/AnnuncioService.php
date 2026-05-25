<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../Entity/EAnnuncio.php';

class AnnuncioService extends BaseService
{
    public function getAnnunciAttiviEntity(int $idCategoria = 0): array
    {
        return $this->toAnnuncioEntities($this->getAnnunciAttivi($idCategoria));
    }

    public function getAnnunciAttivi(int $idCategoria = 0): array
    {
        $whereCategoria = '';
        $params = [];

        if ($idCategoria > 0) {
            $whereCategoria = ' AND a.id_categoria = ?';
            $params[] = $idCategoria;
        }

        $stmt = $this->db->prepare("
            SELECT
                a.*,
                c.nome AS categoria_nome,
                u.username AS venditore_username,
                ab.id_acc_business AS venditore_business_id,
                ab.nome_azienda AS venditore_nome_azienda,
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
            LEFT JOIN account_business ab ON ab.id_utente = a.id_utente
            WHERE a.stato = 'attivo'
            {$whereCategoria}
            ORDER BY a.data_creazione DESC
        ");
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function getAnnunciCasualiEntity(int $limit = 8, ?int $excludeUserId = null, array $excludeAnnuncioIds = []): array
    {
        return $this->toAnnuncioEntities($this->getAnnunciCasuali($limit, $excludeUserId, $excludeAnnuncioIds));
    }

    public function getAnnunciCasuali(int $limit = 8, ?int $excludeUserId = null, array $excludeAnnuncioIds = []): array
    {
        $limit = max(1, min($limit, 24));
        $whereUtente = '';
        $whereAnnunci = '';
        $params = [];

        if ($excludeUserId !== null && $excludeUserId > 0) {
            $whereUtente = ' AND (a.id_utente IS NULL OR a.id_utente <> ?)';
            $params[] = $excludeUserId;
        }

        $excludeAnnuncioIds = array_values(array_filter(array_map('intval', $excludeAnnuncioIds), static fn($id) => $id > 0));

        if (!empty($excludeAnnuncioIds)) {
            $whereAnnunci = ' AND a.id_annuncio NOT IN (' . implode(',', array_fill(0, count($excludeAnnuncioIds), '?')) . ')';
            $params = array_merge($params, $excludeAnnuncioIds);
        }

        $stmt = $this->db->prepare("
            SELECT
                a.*,
                c.nome AS categoria_nome,
                u.username AS venditore_username,
                ab.id_acc_business AS venditore_business_id,
                ab.nome_azienda AS venditore_nome_azienda,
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
            LEFT JOIN account_business ab ON ab.id_utente = a.id_utente
            WHERE a.stato = 'attivo'
            {$whereUtente}
            {$whereAnnunci}
            ORDER BY RAND()
            LIMIT {$limit}
        ");
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function getAnnunciPerInteressiUtente(int $idUtente, int $limit = 8): array
    {
        $this->requirePositiveId($idUtente, 'Utente');
        $limit = max(1, min($limit, 24));

        $categorie = $this->getCategorieInteresseUtente($idUtente);

        if (empty($categorie)) {
            return $this->getAnnunciCasuali($limit, $idUtente);
        }

        $placeholders = implode(',', array_fill(0, count($categorie), '?'));
        $params = array_merge($categorie, [$idUtente]);

        $stmt = $this->db->prepare("
            SELECT
                a.*,
                c.nome AS categoria_nome,
                u.username AS venditore_username,
                ab.id_acc_business AS venditore_business_id,
                ab.nome_azienda AS venditore_nome_azienda,
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
            LEFT JOIN account_business ab ON ab.id_utente = a.id_utente
            WHERE a.stato = 'attivo'
              AND a.id_categoria IN ({$placeholders})
              AND (a.id_utente IS NULL OR a.id_utente <> ?)
            ORDER BY RAND()
            LIMIT {$limit}
        ");
        $stmt->execute($params);

        $annunci = $stmt->fetchAll();

        if (count($annunci) >= $limit) {
            return $annunci;
        }

        $annunciIds = array_map(static fn($annuncio) => (int)($annuncio['id_annuncio'] ?? 0), $annunci);
        $fallback = $this->getAnnunciCasuali($limit - count($annunci), $idUtente, $annunciIds);

        return array_merge($annunci, $fallback);
    }

    public function findEntityById(int $idAnnuncio): ?EAnnuncio
    {
        $annuncio = $this->findById($idAnnuncio);

        return $annuncio ? $this->toAnnuncioEntity($annuncio) : null;
    }

    public function findById(int $idAnnuncio): ?array
    {
        $this->requirePositiveId($idAnnuncio, 'Annuncio');

        $stmt = $this->db->prepare("
            SELECT
                a.*,
                c.nome AS categoria_nome,
                u.username AS venditore_username,
                ab.id_acc_business AS venditore_business_id,
                ab.nome_azienda AS venditore_nome_azienda
            FROM annuncio a
            LEFT JOIN categoria c ON c.id_categoria = a.id_categoria
            LEFT JOIN utente_registrato u ON u.id_utente = a.id_utente
            LEFT JOIN account_business ab ON ab.id_utente = a.id_utente
            WHERE a.id_annuncio = ?
            LIMIT 1
        ");
        $stmt->execute([$idAnnuncio]);

        $annuncio = $stmt->fetch() ?: null;

        if (!$annuncio) {
            return null;
        }

        $annuncio['immagini'] = $this->getImmaginiByAnnuncio($idAnnuncio);

        return array_merge($annuncio, $this->toAnnuncioEntity($annuncio)->toArray());
    }

    public function getByUserId(int $idUtente): array
    {
        return $this->getByUserIdAndStato($idUtente, null);
    }

    public function getByUserIdEntity(int $idUtente): array
    {
        return $this->toAnnuncioEntities($this->getByUserId($idUtente));
    }

    public function getByUserIdAndStato(int $idUtente, ?string $stato = 'attivo'): array
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $whereStato = '';
        $params = [$idUtente];

        if ($stato !== null) {
            $stato = $this->clean($stato);
            $whereStato = ' AND a.stato = ?';
            $params[] = $stato;
        }

        $stmt = $this->db->prepare("
            SELECT
                a.*,
                c.nome AS categoria_nome,
                u.username AS venditore_username,
                ab.id_acc_business AS venditore_business_id,
                ab.nome_azienda AS venditore_nome_azienda,
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
            LEFT JOIN account_business ab ON ab.id_utente = a.id_utente
            WHERE a.id_utente = ? {$whereStato}
            ORDER BY a.data_creazione DESC
        ");
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function getByUserIdAndStatoEntity(int $idUtente, ?string $stato = 'attivo'): array
    {
        return $this->toAnnuncioEntities($this->getByUserIdAndStato($idUtente, $stato));
    }

    public function creaDaEntity(EAnnuncio $annuncio, int $idUtente, array $files = []): int
    {
        return $this->crea($annuncio->toArray(), $idUtente, $files);
    }

    public function crea(array $data, int $idUtente, array $files = []): int
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $titolo = $this->clean($data['titolo'] ?? '');
        $descrizione = $this->clean($data['descrizione'] ?? '');
        $idCategoria = (int) ($data['id_categoria'] ?? 0);
        $statoConservazione = $this->clean($data['stato_conservazione'] ?? '');
        $prezzo = (float) ($data['prezzo'] ?? 0);
        $statiConservazioneValidi = [
            'Nuovo',
            'Usato come nuovo',
            'Ottimo',
            'Buono',
            'Discreto',
            'Scarso',
        ];

        if ($titolo === '' || $idCategoria <= 0 || $statoConservazione === '' || $prezzo <= 0) {
            throw new ServiceException('Compila tutti i campi obbligatori dell’annuncio.');
        }

        if (!in_array($statoConservazione, $statiConservazioneValidi, true)) {
            throw new ServiceException('Stato di conservazione non valido.');
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

    public function aggiorna(int $idAnnuncio, int $idUtente, array $data, array $files = []): void
    {
        $this->requirePositiveId($idAnnuncio, 'Annuncio');
        $this->requirePositiveId($idUtente, 'Utente');

        $annuncio = $this->findEntityById($idAnnuncio);
        if (!$annuncio || (int)($annuncio->getIdUtente() ?? 0) !== $idUtente) {
            throw new ServiceException('Non puoi modificare questo annuncio.');
        }

        if (!$annuncio->isAttivo()) {
            throw new ServiceException('Puoi modificare solo annunci attivi.');
        }

        $titolo = $this->clean($data['titolo'] ?? '');
        $descrizione = $this->clean($data['descrizione'] ?? '');
        $idCategoria = (int) ($data['id_categoria'] ?? 0);
        $statoConservazione = $this->clean($data['stato_conservazione'] ?? '');
        $prezzo = (float) ($data['prezzo'] ?? 0);
        $statiConservazioneValidi = [
            'Nuovo',
            'Usato come nuovo',
            'Ottimo',
            'Buono',
            'Discreto',
            'Scarso',
        ];

        if ($titolo === '' || $idCategoria <= 0 || $statoConservazione === '' || $prezzo <= 0) {
            throw new ServiceException('Compila tutti i campi obbligatori dell’annuncio.');
        }

        if (!in_array($statoConservazione, $statiConservazioneValidi, true)) {
            throw new ServiceException('Stato di conservazione non valido.');
        }

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                UPDATE annuncio
                SET id_categoria = ?,
                    titolo = ?,
                    descrizione = ?,
                    stato_conservazione = ?,
                    prezzo = ?
                WHERE id_annuncio = ? AND id_utente = ? AND stato = 'attivo'
            ");

            $stmt->execute([
                $idCategoria,
                $titolo,
                $descrizione !== '' ? $descrizione : null,
                $statoConservazione,
                $prezzo,
                $idAnnuncio,
                $idUtente,
            ]);

            $this->salvaImmaginiAnnuncio($idAnnuncio, $files);

            $this->db->commit();
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function aggiornaDaEntity(EAnnuncio $annuncio, int $idUtente, array $files = []): void
    {
        $idAnnuncio = $annuncio->getIdAnnuncio();

        if ($idAnnuncio === null) {
            throw new ServiceException('Annuncio non valido.');
        }

        $this->aggiorna($idAnnuncio, $idUtente, $annuncio->toArray(), $files);
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

    public function eliminaImmagine(int $idImmagine, int $idUtente): int
    {
        $this->requirePositiveId($idImmagine, 'Immagine');
        $this->requirePositiveId($idUtente, 'Utente');

        $stmt = $this->db->prepare("
            SELECT i.id_immagine, i.id_annuncio, i.url
            FROM immagine i
            JOIN annuncio a ON a.id_annuncio = i.id_annuncio
            WHERE i.id_immagine = ?
              AND a.id_utente = ?
              AND a.stato = 'attivo'
            LIMIT 1
        ");
        $stmt->execute([$idImmagine, $idUtente]);
        $immagine = $stmt->fetch();

        if (!$immagine) {
            throw new ServiceException('Non puoi rimuovere questa foto.');
        }

        $stmt = $this->db->prepare("DELETE FROM immagine WHERE id_immagine = ?");
        $stmt->execute([$idImmagine]);

        if ($stmt->rowCount() === 0) {
            throw new ServiceException('Foto non rimossa.');
        }

        $this->eliminaFileImmagine((string)($immagine['url'] ?? ''));

        return (int)$immagine['id_annuncio'];
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

    public function searchAnnunci(
        string $keywords,
        int $idCategoria = 0,
        ?float $prezzoMin = null,
        ?float $prezzoMax = null,
        string $ordinamento = 'data_desc',
        ?int $limit = null,
        int $offset = 0,
        ?int $excludeUserId = null
    ): array
    {
        [$whereSql, $params] = $this->buildSearchWhere($keywords, $idCategoria, $prezzoMin, $prezzoMax, $excludeUserId);
        $orderBy = match ($ordinamento) {
            'prezzo_asc' => 'a.prezzo ASC, a.data_creazione DESC',
            'prezzo_desc' => 'a.prezzo DESC, a.data_creazione DESC',
            'data_asc' => 'a.data_creazione ASC',
            default => 'a.data_creazione DESC',
        };

        $limitSql = '';
        if ($limit !== null) {
            $limit = max(1, min($limit, 60));
            $offset = max(0, $offset);
            $limitSql = " LIMIT {$limit} OFFSET {$offset}";
        }

        $stmt = $this->db->prepare("
            SELECT
                a.*,
                c.nome AS categoria_nome,
                u.username AS venditore_username,
                ab.id_acc_business AS venditore_business_id,
                ab.nome_azienda AS venditore_nome_azienda,
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
            LEFT JOIN account_business ab ON ab.id_utente = a.id_utente
            WHERE {$whereSql}
            ORDER BY {$orderBy}
            {$limitSql}
        ");

        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function searchAnnunciEntity(
        string $keywords,
        int $idCategoria = 0,
        ?float $prezzoMin = null,
        ?float $prezzoMax = null,
        string $ordinamento = 'data_desc',
        ?int $limit = null,
        int $offset = 0,
        ?int $excludeUserId = null
    ): array
    {
        return $this->toAnnuncioEntities($this->searchAnnunci(
            $keywords,
            $idCategoria,
            $prezzoMin,
            $prezzoMax,
            $ordinamento,
            $limit,
            $offset,
            $excludeUserId
        ));
    }

    public function countSearchAnnunci(string $keywords, int $idCategoria = 0, ?float $prezzoMin = null, ?float $prezzoMax = null, ?int $excludeUserId = null): int
    {
        [$whereSql, $params] = $this->buildSearchWhere($keywords, $idCategoria, $prezzoMin, $prezzoMax, $excludeUserId);

        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM annuncio a
            WHERE {$whereSql}
        ");
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    private function buildSearchWhere(string $keywords, int $idCategoria = 0, ?float $prezzoMin = null, ?float $prezzoMax = null, ?int $excludeUserId = null): array
    {
        $keywords = $this->clean($keywords);
        $where = ["a.stato = 'attivo'"];
        $params = [];

        if ($keywords !== '') {
            $where[] = "(
                a.titolo LIKE CONCAT('%', ?, '%')
                OR a.descrizione LIKE CONCAT('%', ?, '%')
            )";
            $params[] = $keywords;
            $params[] = $keywords;
        }

        if ($idCategoria > 0) {
            $where[] = 'a.id_categoria = ?';
            $params[] = $idCategoria;
        }

        if ($prezzoMin !== null && $prezzoMin >= 0) {
            $where[] = 'a.prezzo >= ?';
            $params[] = $prezzoMin;
        }

        if ($prezzoMax !== null && $prezzoMax >= 0) {
            $where[] = 'a.prezzo <= ?';
            $params[] = $prezzoMax;
        }

        if ($excludeUserId !== null && $excludeUserId > 0) {
            $where[] = '(a.id_utente IS NULL OR a.id_utente <> ?)';
            $params[] = $excludeUserId;
        }

        return [implode(' AND ', $where), $params];
    }

    private function toAnnuncioEntity(array $annuncio): EAnnuncio
    {
        return EAnnuncio::fromArray($annuncio);
    }

    private function toAnnuncioEntities(array $annunci): array
    {
        return array_map(fn(array $annuncio) => $this->toAnnuncioEntity($annuncio), $annunci);
    }

    private function getCategorieInteresseUtente(int $idUtente): array
    {
        $stmt = $this->db->prepare("
            SELECT id_categoria
            FROM (
                SELECT a.id_categoria, COUNT(*) AS peso
                FROM preferito p
                JOIN annuncio a ON a.id_annuncio = p.id_annuncio
                WHERE p.id_utente = ?
                GROUP BY a.id_categoria

                UNION ALL

                SELECT a.id_categoria, COUNT(*) AS peso
                FROM carrello c
                JOIN elemento_carrello e ON e.id_carrello = c.id_carrello
                JOIN annuncio a ON a.id_annuncio = e.id_annuncio
                WHERE c.id_utente = ?
                GROUP BY a.id_categoria

                UNION ALL

                SELECT a.id_categoria, COUNT(*) AS peso
                FROM pagamento p
                JOIN annuncio a ON a.id_annuncio = p.id_annuncio
                WHERE p.id_acquirente = ?
                GROUP BY a.id_categoria
            ) interessi
            WHERE id_categoria IS NOT NULL
            GROUP BY id_categoria
            ORDER BY SUM(peso) DESC
            LIMIT 5
        ");
        $stmt->execute([$idUtente, $idUtente, $idUtente]);

        return array_map('intval', array_column($stmt->fetchAll(), 'id_categoria'));
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

    private function eliminaFileImmagine(string $url): void
    {
        $url = trim($url);

        if ($url === '' || str_contains($url, '..')) {
            return;
        }

        $path = realpath(__DIR__ . '/../../public/' . ltrim(str_replace('\\', '/', $url), '/'));
        $publicRoot = realpath(__DIR__ . '/../../public');

        if ($path && $publicRoot && str_starts_with($path, $publicRoot) && is_file($path)) {
            @unlink($path);
        }
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

        $stmtCount = $this->db->prepare("SELECT COUNT(*) FROM immagine WHERE id_annuncio = ?");
        $stmtCount->execute([$idAnnuncio]);
        $immaginiEsistenti = (int) $stmtCount->fetchColumn();

        if ($immaginiEsistenti >= $maxFile) {
            return;
        }

        $ordine = $immaginiEsistenti;
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
