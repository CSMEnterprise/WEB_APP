<?php

namespace App\Foundation;

use PDO;
use RuntimeException;

/**
 * Wrapper singleton della connessione PDO.
 * Centralizza accesso al database e creazione delle classi Foundation.
 */
class FDataBase
{
    private static ?self $instance = null;
    private PDO $db;

    /**
     * Costruttore privato: l'istanza passa sempre da init/getInstance.
     */
    private function __construct(PDO $db)
    {
        $this->db = $db;
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    /**
     * Registra la connessione PDO corrente per tutto il layer Foundation.
     */
    public static function init(PDO $db): self
    {
        self::$instance = new self($db);

        return self::$instance;
    }

    /**
     * Restituisce l'istanza gia inizializzata o la crea dal PDO globale.
     */
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

    /**
     * Espone la connessione PDO quando serve una query diretta.
     */
    public function getConnection(): PDO
    {
        return $this->db;
    }

    /**
     * Istanzia una classe Foundation passando la connessione condivisa.
     */
    public function table(string $foundationClass): object
    {
        if (!class_exists($foundationClass)) {
            throw new RuntimeException('Classe Foundation non trovata: ' . $foundationClass);
        }

        return new $foundationClass($this->db);
    }

    /**
     * Avvia una transazione sul PDO condiviso.
     */
    public function beginTransaction(): bool
    {
        return $this->db->beginTransaction();
    }

    /**
     * Conferma la transazione corrente.
     */
    public function commit(): bool
    {
        return $this->db->commit();
    }

    /**
     * Annulla la transazione corrente.
     */
    public function rollBack(): bool
    {
        return $this->db->rollBack();
    }

    /**
     * Indica se la connessione e dentro una transazione.
     */
    public function inTransaction(): bool
    {
        return $this->db->inTransaction();
    }

    /**
     * Conteggio rapido usato per statistiche di dashboard.
     */
    public function count(string $table, string $where = ''): int
    {
        // Il nome tabella viene validato perche non puo essere parametrizzato con PDO.
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
