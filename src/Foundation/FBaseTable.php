<?php

namespace App\Foundation;

use App\Entity\EBaseEntity;
use PDO;

abstract class FBaseTable
{
    protected PDO $db;
    private array $columnCache = [];

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    abstract protected function tableName(): string;

    abstract protected function primaryKey(): string;

    abstract protected function entityClass(): string;

    abstract protected function columns(): array;

    public function find(int $id): ?EBaseEntity
    {
        return $this->fetchEntity(
            'SELECT * FROM ' . $this->table() . ' WHERE ' . $this->column($this->primaryKey()) . ' = ? LIMIT 1',
            [$id]
        );
    }

    public function all(string $orderBy = ''): array
    {
        $sql = 'SELECT * FROM ' . $this->table();

        if ($orderBy !== '') {
            $sql .= ' ORDER BY ' . $orderBy;
        }

        return $this->fetchEntities($sql);
    }

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

    public function existByField(string $field, mixed $value): bool
    {
        return (int) $this->fetchColumn(
            'SELECT COUNT(*) FROM ' . $this->table() . ' WHERE ' . $this->column($field) . ' = ?',
            [$value]
        ) > 0;
    }

    public function deleteByField(string $field, mixed $value): bool
    {
        return $this->execute(
            'DELETE FROM ' . $this->table() . ' WHERE ' . $this->column($field) . ' = ?',
            [$value]
        ) > 0;
    }

    public function updateFieldBy(string $field, mixed $newValue, string $pk, mixed $value): bool
    {
        return $this->execute(
            'UPDATE ' . $this->table() . ' SET ' . $this->column($field) . ' = ? WHERE ' . $this->column($pk) . ' = ?',
            [$newValue, $value]
        ) > 0;
    }

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

    public function deleteById(int $id): bool
    {
        return $this->execute(
            'DELETE FROM ' . $this->table() . ' WHERE ' . $this->column($this->primaryKey()) . ' = ?',
            [$id]
        ) > 0;
    }

    protected function fetchEntity(string $sql, array $params = []): ?EBaseEntity
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();

        return $row ? $this->hydrate($row) : null;
    }

    protected function fetchEntities(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return array_map(fn(array $row) => $this->hydrate($row), $stmt->fetchAll());
    }

    protected function fetchRows(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    protected function fetchColumn(string $sql, array $params = []): mixed
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn();
    }

    protected function execute(string $sql, array $params = []): int
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount();
    }

    protected function hydrate(array $row): EBaseEntity
    {
        $class = $this->entityClass();

        return $class::fromArray($row);
    }

    protected function writableRow(EBaseEntity|array $source): array
    {
        $row = $source instanceof EBaseEntity ? $source->toArray() : $source;
        $allowed = array_flip($this->columns());

        return array_intersect_key($row, $allowed);
    }

    protected function table(): string
    {
        return $this->column($this->tableName());
    }

    protected function column(string $identifier): string
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $identifier)) {
            throw new \InvalidArgumentException('Identificatore SQL non valido: ' . $identifier);
        }

        return '`' . $identifier . '`';
    }

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
