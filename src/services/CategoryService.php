<?php

require_once __DIR__ . '/BaseService.php';

class CategoryService extends BaseService
{
    public function all(): array
    {
        return $this->fetchAll(
            'SELECT c.*, p.nome AS nome_padre
             FROM categoria c
             LEFT JOIN categoria p ON p.id_categoria = c.id_padre
             ORDER BY COALESCE(p.nome, c.nome), c.nome'
        );
    }

    public function findById(int $categoryId): ?array
    {
        $this->requirePositiveInt($categoryId, 'id_categoria');

        return $this->fetchOne(
            'SELECT * FROM categoria WHERE id_categoria = :id',
            [':id' => $categoryId]
        );
    }

    public function create(string $nome, ?int $parentId = null): int
    {
        $this->requireNotEmpty($nome, 'nome categoria');

        $this->execute(
            'INSERT INTO categoria (nome, id_padre) VALUES (:nome, :id_padre)',
            [
                ':nome' => trim($nome),
                ':id_padre' => $parentId,
            ]
        );

        return $this->lastInsertId();
    }
}
