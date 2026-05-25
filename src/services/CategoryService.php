<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../Entity/ECategoria.php';

class CategoryService extends BaseService
{
    public function getAllEntity(): array
    {
        return $this->toCategoriaEntities($this->getAll());
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT id_categoria, nome, id_padre
            FROM categoria
            ORDER BY nome ASC
        ");

        return $stmt->fetchAll();
    }

    private function toCategoriaEntities(array $categorie): array
    {
        return array_map(static fn(array $categoria) => ECategoria::fromArray($categoria), $categorie);
    }
}
