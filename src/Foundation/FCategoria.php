<?php

namespace App\Foundation;

use App\Entity\ECategoria;

class FCategoria extends FBaseTable
{
    protected function tableName(): string
    {
        return 'categoria';
    }

    protected function primaryKey(): string
    {
        return 'id_categoria';
    }

    protected function entityClass(): string
    {
        return ECategoria::class;
    }

    protected function columns(): array
    {
        return ['id_categoria', 'nome', 'id_padre'];
    }

    public function allOrdered(): array
    {
        return $this->all('`nome` ASC');
    }
}
