<?php

namespace App\Foundation;

use PDO;
use RuntimeException;

class FDataBase
{
    private static ?self $instance = null;
    private PDO $db;

    private function __construct(PDO $db)
    {
        $this->db = $db;
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public static function init(PDO $db): self
    {
        self::$instance = new self($db);

        return self::$instance;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            if (!isset($GLOBALS['pdo']) || !$GLOBALS['pdo'] instanceof PDO) {
                throw new RuntimeException('FDataBase non inizializzato.');
            }

            self::init($GLOBALS['pdo']);
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->db;
    }

    public function table(string $foundationClass): object
    {
        if (!class_exists($foundationClass)) {
            throw new RuntimeException('Classe Foundation non trovata: ' . $foundationClass);
        }

        return new $foundationClass($this->db);
    }

    public function beginTransaction(): bool
    {
        return $this->db->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->db->commit();
    }

    public function rollBack(): bool
    {
        return $this->db->rollBack();
    }

    public function inTransaction(): bool
    {
        return $this->db->inTransaction();
    }

    public function count(string $table, string $where = ''): int
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            throw new RuntimeException('Tabella non valida: ' . $table);
        }

        $sql = 'SELECT COUNT(*) FROM `' . $table . '`';

        if ($where !== '') {
            $sql .= ' WHERE ' . $where;
        }

        return (int) $this->db->query($sql)->fetchColumn();
    }
}
