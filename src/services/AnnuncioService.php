<?php

require_once __DIR__ . '/BaseService.php';

class AnnuncioService extends BaseService
{
    private const STATI_CONSERVAZIONE = ['Nuovo', 'Ottimo', 'Buono', 'Discreto', 'Da restaurare'];
    private const MODALITA_CONSEGNA = ['Spedizione', 'Ritiro_a_mano', 'Entrambi'];

    public function all(array $filters = []): array
    {
        $where = ['a.stato = :stato'];
        $params = [':stato' => $filters['stato'] ?? 'attivo'];

        if (!empty($filters['categoria'])) {
            $where[] = 'a.id_categoria = :categoria';
            $params[':categoria'] = (int) $filters['categoria'];
        }

        if (!empty($filters['q'])) {
            $where[] = '(a.titolo LIKE :q OR a.descrizione LIKE :q)';
            $params[':q'] = '%' . trim($filters['q']) . '%';
        }

        if (!empty($filters['prezzo_min'])) {
            $where[] = 'a.prezzo >= :prezzo_min';
            $params[':prezzo_min'] = (float) $filters['prezzo_min'];
        }

        if (!empty($filters['prezzo_max'])) {
            $where[] = 'a.prezzo <= :prezzo_max';
            $params[':prezzo_max'] = (float) $filters['prezzo_max'];
        }

        $sql = '
            SELECT a.*, c.nome AS categoria_nome,
                   u.username AS venditore_username,
                   b.nome_azienda AS business_nome,
                   img.url AS immagine_principale
            FROM annuncio a
            INNER JOIN categoria c ON c.id_categoria = a.id_categoria
            LEFT JOIN utente_registrato u ON u.id_utente = a.id_utente
            LEFT JOIN account_business b ON b.id_acc_business = a.id_business
            LEFT JOIN immagine img ON img.id_annuncio = a.id_annuncio AND img.ordine = 0
            WHERE ' . implode(' AND ', $where) . '
            ORDER BY a.data_creazione DESC';

        return $this->fetchAll($sql, $params);
    }

    public function findById(int $annuncioId): ?array
    {
        $this->requirePositiveInt($annuncioId, 'id_annuncio');

        return $this->fetchOne(
            'SELECT a.*, c.nome AS categoria_nome,
                    u.username AS venditore_username,
                    u.email AS venditore_email,
                    b.nome_azienda AS business_nome,
                    b.email_aziendale AS business_email
             FROM annuncio a
             INNER JOIN categoria c ON c.id_categoria = a.id_categoria
             LEFT JOIN utente_registrato u ON u.id_utente = a.id_utente
             LEFT JOIN account_business b ON b.id_acc_business = a.id_business
             WHERE a.id_annuncio = :id',
            [':id' => $annuncioId]
        );
    }

    public function create(array $data): int
    {
        $this->validateAnnuncioData($data);

        $this->execute(
            'INSERT INTO annuncio
                (id_utente, id_business, id_categoria, titolo, descrizione, stato_conservazione, prezzo, modalita_consegna, data_scadenza)
             VALUES
                (:id_utente, :id_business, :id_categoria, :titolo, :descrizione, :stato_conservazione, :prezzo, :modalita_consegna, :data_scadenza)',
            [
                ':id_utente' => !empty($data['id_utente']) ? (int) $data['id_utente'] : null,
                ':id_business' => !empty($data['id_business']) ? (int) $data['id_business'] : null,
                ':id_categoria' => (int) $data['id_categoria'],
                ':titolo' => trim($data['titolo']),
                ':descrizione' => trim($data['descrizione'] ?? '') ?: null,
                ':stato_conservazione' => $data['stato_conservazione'],
                ':prezzo' => (float) $data['prezzo'],
                ':modalita_consegna' => $data['modalita_consegna'],
                ':data_scadenza' => $data['data_scadenza'] ?? null,
            ]
        );

        return $this->lastInsertId();
    }

    public function update(int $annuncioId, array $data): bool
    {
        $this->requirePositiveInt($annuncioId, 'id_annuncio');
        $this->validateAnnuncioData($data, false);

        return $this->execute(
            'UPDATE annuncio
             SET id_categoria = :id_categoria,
                 titolo = :titolo,
                 descrizione = :descrizione,
                 stato_conservazione = :stato_conservazione,
                 prezzo = :prezzo,
                 modalita_consegna = :modalita_consegna,
                 data_scadenza = :data_scadenza
             WHERE id_annuncio = :id',
            [
                ':id_categoria' => (int) $data['id_categoria'],
                ':titolo' => trim($data['titolo']),
                ':descrizione' => trim($data['descrizione'] ?? '') ?: null,
                ':stato_conservazione' => $data['stato_conservazione'],
                ':prezzo' => (float) $data['prezzo'],
                ':modalita_consegna' => $data['modalita_consegna'],
                ':data_scadenza' => $data['data_scadenza'] ?? null,
                ':id' => $annuncioId,
            ]
        );
    }

    public function markAsSold(int $annuncioId): bool
    {
        $this->requirePositiveInt($annuncioId, 'id_annuncio');

        return $this->execute(
            "UPDATE annuncio SET stato = 'venduto' WHERE id_annuncio = :id",
            [':id' => $annuncioId]
        );
    }

    public function delete(int $annuncioId): bool
    {
        $this->requirePositiveInt($annuncioId, 'id_annuncio');

        return $this->execute(
            'DELETE FROM annuncio WHERE id_annuncio = :id',
            [':id' => $annuncioId]
        );
    }

    public function userOwnsAnnuncio(int $userId, int $annuncioId): bool
    {
        $row = $this->fetchOne(
            'SELECT id_annuncio FROM annuncio WHERE id_annuncio = :annuncio AND id_utente = :utente LIMIT 1',
            [
                ':annuncio' => $annuncioId,
                ':utente' => $userId,
            ]
        );

        return $row !== null;
    }

    private function validateAnnuncioData(array $data, bool $checkOwner = true): void
    {
        if ($checkOwner && empty($data['id_utente']) && empty($data['id_business'])) {
            throw new ServiceException('Un annuncio deve essere associato a un utente o a un account business.');
        }

        $this->requirePositiveInt((int) ($data['id_categoria'] ?? 0), 'id_categoria');
        $this->requireNotEmpty($data['titolo'] ?? '', 'titolo');

        if (!in_array($data['stato_conservazione'] ?? '', self::STATI_CONSERVAZIONE, true)) {
            throw new ServiceException('Stato di conservazione non valido.');
        }

        if (!in_array($data['modalita_consegna'] ?? '', self::MODALITA_CONSEGNA, true)) {
            throw new ServiceException('Modalità di consegna non valida.');
        }

        if (!isset($data['prezzo']) || (float) $data['prezzo'] < 0) {
            throw new ServiceException('Prezzo non valido.');
        }
    }
}
