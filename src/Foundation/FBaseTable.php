<?php

namespace App\Foundation;

use App\Entity\EBaseEntity;
use PDO;

/**
 * Classe base per tutte le classi Foundation che rappresentano una tabella.
 * Offre CRUD generico, query helper e idratazione delle righe SQL nelle Entity.
 */
abstract class FBaseTable
{
    protected PDO $db;
    private array $columnCache = [];

    /**
     * Riceve la connessione PDO condivisa e imposta errori/fetch mode coerenti.
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    /**
     * Nome fisico della tabella nel database.
     */
    abstract protected function tableName(): string;

    /**
     * Nome della chiave primaria della tabella.
     */
    abstract protected function primaryKey(): string;

    /**
     * Classe Entity usata per trasformare le righe SQL in oggetti applicativi.
     */
    abstract protected function entityClass(): string;

    /**
     * Lista bianca delle colonne scrivibili con insert/update generici.
     */
    abstract protected function columns(): array;

    /**
     * Cerca un record per chiave primaria.
     */
    public function find(int $id): ?EBaseEntity
    {
        return $this->fetchEntity(
            'SELECT * FROM ' . $this->table() . ' WHERE ' . $this->column($this->primaryKey()) . ' = ? LIMIT 1',
            [$id]
        );
    }

    /**
     * Restituisce tutti i record, con ordinamento opzionale controllato dal chiamante.
     */
    public function all(string $orderBy = ''): array
    {
        $sql = 'SELECT * FROM ' . $this->table();

        if ($orderBy !== '') {
            $sql .= ' ORDER BY ' . $orderBy;
        }

        return $this->fetchEntities($sql);
    }

    /**
     * Carica record tramite campo arbitrario: null, singola entity o lista di entity.
     */
    public function loadByField(string $field, mixed $value): mixed
    {
        $entities = $this->fetchEntities(
            'SELECT * FROM ' . $this->table() . ' WHERE ' . $this->column($field) . ' = ?',
            [$value]
        );

        return match (count($entities)) {
            0 => null,
            1 => $entities[0],
            default => $entities,
        };
    }

    /**
     * Verifica l'esistenza di almeno un record per campo/valore.
     */
    public function existByField(string $field, mixed $value): bool
    {
        return (int) $this->fetchColumn(
            'SELECT COUNT(*) FROM ' . $this->table() . ' WHERE ' . $this->column($field) . ' = ?',
            [$value]
        ) > 0;
    }

    /**
     * Cancella record filtrando per campo/valore.
     */
    public function deleteByField(string $field, mixed $value): bool
    {
        return $this->execute(
            'DELETE FROM ' . $this->table() . ' WHERE ' . $this->column($field) . ' = ?',
            [$value]
        ) > 0;
    }

    /**
     * Aggiorna un singolo campo usando un altro campo come filtro.
     */
    public function updateFieldBy(string $field, mixed $newValue, string $pk, mixed $value): bool
    {
        return $this->execute(
            'UPDATE ' . $this->table() . ' SET ' . $this->column($field) . ' = ? WHERE ' . $this->column($pk) . ' = ?',
            [$newValue, $value]
        ) > 0;
    }

    /**
     * Inserisce una entity o array limitando i dati alle sole colonne ammesse.
     */
    public function insert(EBaseEntity|array $source): int
    {
        $row = $this->writableRow($source);
        $primaryKey = $this->primaryKey();

        if (array_key_exists($primaryKey, $row) && ($row[$primaryKey] === null || $row[$primaryKey] === 0)) {
            unset($row[$primaryKey]);
        }

        $columns = array_keys($row);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table(),
            implode(', ', array_map(fn(string $column) => $this->column($column), $columns)),
            implode(', ', $placeholders)
        );

        $this->execute($sql, array_values($row));

        return (int) $this->db->lastInsertId();
    }

    /**
     * Aggiorna un record per primary key usando solo colonne scrivibili.
     */
    public function updateById(int $id, EBaseEntity|array $source): bool
    {
        $row = $this->writableRow($source);
        unset($row[$this->primaryKey()]);

        if (empty($row)) {
            return false;
        }

        $assignments = array_map(
            fn(string $column) => $this->column($column) . ' = ?',
            array_keys($row)
        );

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s = ?',
            $this->table(),
            implode(', ', $assignments),
            $this->column($this->primaryKey())
        );

        $params = array_values($row);
        $params[] = $id;

        return $this->execute($sql, $params) > 0;
    }

    /**
     * Elimina un record per primary key.
     */
    public function deleteById(int $id): bool
    {
        return $this->execute(
            'DELETE FROM ' . $this->table() . ' WHERE ' . $this->column($this->primaryKey()) . ' = ?',
            [$id]
        ) > 0;
    }

    /**
     * Esegue una SELECT che deve restituire al massimo una entity.
     */
    protected function fetchEntity(string $sql, array $params = []): ?EBaseEntity
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    /**
     * Esegue una SELECT e converte tutte le righe nella entity della tabella.
     */
    protected function fetchEntities(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return array_map(fn(array $row) => $this->hydrate($row), $stmt->fetchAll());
    }

    /**
     * Esegue una SELECT lasciando le righe come array associativi.
     */
    protected function fetchRows(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Recupera una singola colonna, utile per count, exists e medie.
     */
    protected function fetchColumn(string $sql, array $params = []): mixed
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn();
    }

    /**
     * Esegue INSERT/UPDATE/DELETE e restituisce le righe impattate.
     */
    protected function execute(string $sql, array $params = []): int
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount();
    }

    /**
     * Crea la entity concreta partendo dalla riga del database.
     */
    protected function hydrate(array $row): EBaseEntity
    {
        $class = $this->entityClass();

        return $class::fromArray($row);
    }

    /**
     * Filtra una entity/array togliendo campi non presenti nella tabella.
     */
    protected function writableRow(EBaseEntity|array $source): array
    {
        $row = $source instanceof EBaseEntity ? $source->toArray() : $source;
        $allowed = array_flip($this->columns());

        return array_intersect_key($row, $allowed);
    }

    /**
     * Nome tabella quotato per SQL.
     */
    protected function table(): string
    {
        return $this->column($this->tableName());
    }

    /**
     * Quota identificatori SQL validando che non contengano caratteri pericolosi.
     */
    protected function column(string $identifier): string
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $identifier)) {
            throw new \InvalidArgumentException('Identificatore SQL non valido: ' . $identifier);
        }

        return '`' . $identifier . '`';
    }

    /**
     * Controlla una colonna reale nel database e memorizza il risultato per tabella.
     */
    protected function hasColumn(string $column): bool
    {
        $table = $this->tableName();
        $cacheKey = $table . '.' . $column;

        if (!array_key_exists($cacheKey, $this->columnCache)) {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
                throw new \InvalidArgumentException('Colonna SQL non valida: ' . $column);
            }

            $stmt = $this->db->query('SHOW COLUMNS FROM ' . $this->table() . ' LIKE ' . $this->db->quote($column));
            $this->columnCache[$cacheKey] = (bool) $stmt->fetch();
        }

        return $this->columnCache[$cacheKey];
    }
}
