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

    protected function lastInsertId(): int
    {
        return (int) $this->db->lastInsertId();
    }

    protected function requirePositiveId(int $id, string $fieldName = 'ID'): void
    {
        if ($id <= 0) {
            throw new ServiceException($fieldName . ' non valido.');
        }
    }

    protected function clean(?string $value): string
    {
        return trim((string) $value);
    }
}
