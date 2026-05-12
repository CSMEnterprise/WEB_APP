<?php

require_once __DIR__ . '/BaseService.php';

class CategoryService extends BaseService
{
    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT id_categoria, nome, id_padre
            FROM categoria
            ORDER BY nome ASC
        ");

        return $stmt->fetchAll();
    }
}
