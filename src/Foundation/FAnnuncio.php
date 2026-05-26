<?php

namespace App\Foundation;

use App\Entity\EAnnuncio;

class FAnnuncio extends FBaseTable
{
    private FImmagine $immagini;

    public function __construct(\PDO $db)
    {
        parent::__construct($db);
        $this->immagini = new FImmagine($db);
    }

    protected function tableName(): string
    {
        return 'annuncio';
    }

    protected function primaryKey(): string
    {
        return 'id_annuncio';
    }

    protected function entityClass(): string
    {
        return EAnnuncio::class;
    }

    protected function columns(): array
    {
        return [
            'id_annuncio',
            'id_utente',
            'id_business',
            'id_categoria',
            'titolo',
            'descrizione',
            'stato_conservazione',
            'prezzo',
            'modalita_consegna',
            'stato',
            'data_creazione',
            'data_scadenza',
        ];
    }

    public function attivi(int $idCategoria = 0): array
    {
        $whereCategoria = '';
        $params = [];

        if ($idCategoria > 0) {
            $whereCategoria = ' AND a.`id_categoria` = ?';
            $params[] = $idCategoria;
        }

        return $this->fetchEntities($this->selectWithDetails() . "
            WHERE a.`stato` = 'attivo'
            {$whereCategoria}
            ORDER BY a.`data_creazione` DESC
        ", $params);
    }

    public function casuali(int $limit = 8, ?int $excludeUserId = null, array $excludeAnnuncioIds = []): array
    {
        $limit = max(1, min($limit, 24));
        $whereUtente = '';
        $whereAnnunci = '';
        $params = [];

        if ($excludeUserId !== null && $excludeUserId > 0) {
            $whereUtente = ' AND (a.`id_utente` IS NULL OR a.`id_utente` <> ?)';
            $params[] = $excludeUserId;
        }

        $excludeAnnuncioIds = array_values(array_filter(array_map('intval', $excludeAnnuncioIds), static fn($id) => $id > 0));

        if (!empty($excludeAnnuncioIds)) {
            $whereAnnunci = ' AND a.`id_annuncio` NOT IN (' . implode(',', array_fill(0, count($excludeAnnuncioIds), '?')) . ')';
            $params = array_merge($params, $excludeAnnuncioIds);
        }

        return $this->fetchEntities($this->selectWithDetails() . "
            WHERE a.`stato` = 'attivo'
            {$whereUtente}
            {$whereAnnunci}
            ORDER BY RAND()
            LIMIT {$limit}
        ", $params);
    }

    public function perInteressiUtente(int $idUtente, int $limit = 8): array
    {
        $limit = max(1, min($limit, 24));
        $categorie = $this->categorieInteresseUtente($idUtente);

        if (empty($categorie)) {
            return $this->casuali($limit, $idUtente);
        }

        $placeholders = implode(',', array_fill(0, count($categorie), '?'));
        $params = array_merge($categorie, [$idUtente]);

        $annunci = $this->fetchEntities($this->selectWithDetails() . "
            WHERE a.`stato` = 'attivo'
              AND a.`id_categoria` IN ({$placeholders})
              AND (a.`id_utente` IS NULL OR a.`id_utente` <> ?)
            ORDER BY RAND()
            LIMIT {$limit}
        ", $params);

        if (count($annunci) >= $limit) {
            return $annunci;
        }

        $annunciIds = array_map(static fn(EAnnuncio $annuncio) => (int) ($annuncio->getIdAnnuncio() ?? 0), $annunci);
        $fallback = $this->casuali($limit - count($annunci), $idUtente, $annunciIds);

        return array_merge($annunci, $fallback);
    }

    public function findWithDetails(int $idAnnuncio): ?EAnnuncio
    {
        $entity = $this->fetchEntity($this->selectWithDetails() . '
            WHERE a.`id_annuncio` = ?
            LIMIT 1
        ', [$idAnnuncio]);

        if (!$entity instanceof EAnnuncio) {
            return null;
        }

        $entity->setImmagini($this->immagini->byAnnuncio($idAnnuncio));

        return $entity;
    }

    public function byUserIdAndStato(int $idUtente, ?string $stato = 'attivo'): array
    {
        $whereStato = '';
        $params = [$idUtente];

        if ($stato !== null) {
            $whereStato = ' AND a.`stato` = ?';
            $params[] = trim($stato);
        }

        return $this->fetchEntities($this->selectWithDetails() . "
            WHERE a.`id_utente` = ? {$whereStato}
            ORDER BY a.`data_creazione` DESC
        ", $params);
    }

    public function createForUser(EAnnuncio $annuncio, int $idUtente): int
    {
        $row = $annuncio->toArray();
        $row['id_utente'] = $idUtente;
        $row['id_business'] = null;
        $row['modalita_consegna'] = $row['modalita_consegna'] ?? 'Consegna';
        $row['stato'] = $row['stato'] ?? 'attivo';
        unset($row['id_annuncio'], $row['data_creazione'], $row['data_scadenza']);

        return $this->insert($row);
    }

    public function updateForUser(int $idAnnuncio, int $idUtente, EAnnuncio $annuncio): bool
    {
        $row = $annuncio->toArray();
        $params = [
            (int) $row['id_categoria'],
            (string) $row['titolo'],
            $row['descrizione'] !== '' ? $row['descrizione'] : null,
            (string) $row['stato_conservazione'],
            (float) $row['prezzo'],
            $idAnnuncio,
            $idUtente,
        ];

        return $this->execute("
            UPDATE `annuncio`
            SET `id_categoria` = ?,
                `titolo` = ?,
                `descrizione` = ?,
                `stato_conservazione` = ?,
                `prezzo` = ?
            WHERE `id_annuncio` = ?
              AND `id_utente` = ?
              AND `stato` = 'attivo'
        ", $params) > 0;
    }

    public function deleteForUser(int $idAnnuncio, int $idUtente): bool
    {
        return $this->execute(
            'DELETE FROM `annuncio` WHERE `id_annuncio` = ? AND `id_utente` = ?',
            [$idAnnuncio, $idUtente]
        ) > 0;
    }

    public function deleteByAdmin(int $idAnnuncio): void
    {
        $this->deleteById($idAnnuncio);
    }

    public function markSold(int $idAnnuncio): void
    {
        $this->execute(
            "UPDATE `annuncio` SET `stato` = 'venduto' WHERE `id_annuncio` = ?",
            [$idAnnuncio]
        );
    }

    public function search(
        string $keywords,
        int $idCategoria = 0,
        ?float $prezzoMin = null,
        ?float $prezzoMax = null,
        string $ordinamento = 'data_desc',
        ?int $limit = null,
        int $offset = 0,
        ?int $excludeUserId = null
    ): array {
        [$whereSql, $params] = $this->buildSearchWhere($keywords, $idCategoria, $prezzoMin, $prezzoMax, $excludeUserId);
        $orderBy = match ($ordinamento) {
            'prezzo_asc' => 'a.`prezzo` ASC, a.`data_creazione` DESC',
            'prezzo_desc' => 'a.`prezzo` DESC, a.`data_creazione` DESC',
            'data_asc' => 'a.`data_creazione` ASC',
            default => 'a.`data_creazione` DESC',
        };

        $limitSql = '';
        if ($limit !== null) {
            $limit = max(1, min($limit, 60));
            $offset = max(0, $offset);
            $limitSql = " LIMIT {$limit} OFFSET {$offset}";
        }

        return $this->fetchEntities($this->selectWithDetails() . "
            WHERE {$whereSql}
            ORDER BY {$orderBy}
            {$limitSql}
        ", $params);
    }

    public function countSearch(string $keywords, int $idCategoria = 0, ?float $prezzoMin = null, ?float $prezzoMax = null, ?int $excludeUserId = null): int
    {
        [$whereSql, $params] = $this->buildSearchWhere($keywords, $idCategoria, $prezzoMin, $prezzoMax, $excludeUserId);

        return (int) $this->fetchColumn("
            SELECT COUNT(*)
            FROM `annuncio` a
            WHERE {$whereSql}
        ", $params);
    }

    private function selectWithDetails(): string
    {
        return "
            SELECT
                a.*,
                c.`nome` AS categoria_nome,
                u.`username` AS venditore_username,
                ab.`id_acc_business` AS venditore_business_id,
                ab.`nome_azienda` AS venditore_nome_azienda,
                (
                    SELECT i.`url`
                    FROM `immagine` i
                    WHERE i.`id_annuncio` = a.`id_annuncio`
                    ORDER BY i.`ordine` ASC, i.`id_immagine` ASC
                    LIMIT 1
                ) AS immagine_principale
            FROM `annuncio` a
            LEFT JOIN `categoria` c ON c.`id_categoria` = a.`id_categoria`
            LEFT JOIN `utente_registrato` u ON u.`id_utente` = a.`id_utente`
            LEFT JOIN `account_business` ab ON ab.`id_utente` = a.`id_utente`
        ";
    }

    private function buildSearchWhere(string $keywords, int $idCategoria = 0, ?float $prezzoMin = null, ?float $prezzoMax = null, ?int $excludeUserId = null): array
    {
        $keywords = trim($keywords);
        $where = ["a.`stato` = 'attivo'"];
        $params = [];

        if ($keywords !== '') {
            $where[] = "(
                a.`titolo` LIKE CONCAT('%', ?, '%')
                OR a.`descrizione` LIKE CONCAT('%', ?, '%')
            )";
            $params[] = $keywords;
            $params[] = $keywords;
        }

        if ($idCategoria > 0) {
            $where[] = 'a.`id_categoria` = ?';
            $params[] = $idCategoria;
        }

        if ($prezzoMin !== null && $prezzoMin >= 0) {
            $where[] = 'a.`prezzo` >= ?';
            $params[] = $prezzoMin;
        }

        if ($prezzoMax !== null && $prezzoMax >= 0) {
            $where[] = 'a.`prezzo` <= ?';
            $params[] = $prezzoMax;
        }

        if ($excludeUserId !== null && $excludeUserId > 0) {
            $where[] = '(a.`id_utente` IS NULL OR a.`id_utente` <> ?)';
            $params[] = $excludeUserId;
        }

        return [implode(' AND ', $where), $params];
    }

    private function categorieInteresseUtente(int $idUtente): array
    {
        $rows = $this->fetchRows("
            SELECT `id_categoria`
            FROM (
                SELECT a.`id_categoria`, COUNT(*) AS peso
                FROM `preferito` p
                JOIN `annuncio` a ON a.`id_annuncio` = p.`id_annuncio`
                WHERE p.`id_utente` = ?
                GROUP BY a.`id_categoria`

                UNION ALL

                SELECT a.`id_categoria`, COUNT(*) AS peso
                FROM `carrello` c
                JOIN `elemento_carrello` e ON e.`id_carrello` = c.`id_carrello`
                JOIN `annuncio` a ON a.`id_annuncio` = e.`id_annuncio`
                WHERE c.`id_utente` = ?
                GROUP BY a.`id_categoria`

                UNION ALL

                SELECT a.`id_categoria`, COUNT(*) AS peso
                FROM `pagamento` p
                JOIN `annuncio` a ON a.`id_annuncio` = p.`id_annuncio`
                WHERE p.`id_acquirente` = ?
                GROUP BY a.`id_categoria`
            ) interessi
            WHERE `id_categoria` IS NOT NULL
            GROUP BY `id_categoria`
            ORDER BY SUM(peso) DESC
            LIMIT 5
        ", [$idUtente, $idUtente, $idUtente]);

        return array_map('intval', array_column($rows, 'id_categoria'));
    }
}
