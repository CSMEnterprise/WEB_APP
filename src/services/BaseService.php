<?php

require_once __DIR__ . '/ServiceException.php';

abstract class BaseService
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    protected function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    protected function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    protected function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    protected function lastInsertId(): int
    {
        return (int) $this->db->lastInsertId();
    }

    protected function requirePositiveInt(int $value, string $fieldName): void
    {
        if ($value <= 0) {
            throw new ServiceException("Il campo {$fieldName} non è valido.");
        }
    }

    protected function requireNotEmpty(?string $value, string $fieldName): void
    {
        if (trim((string) $value) === '') {
            throw new ServiceException("Il campo {$fieldName} è obbligatorio.");
        }
    }
}
