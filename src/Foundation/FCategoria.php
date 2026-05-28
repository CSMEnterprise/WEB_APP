<?php

namespace App\Foundation;

use App\Entity\ECategoria;

/**
 * Repository delle categorie usate per filtri, header e annunci.
 */
class FCategoria extends FBaseTable
{
    /**
     * Nome tabella categorie.
     */
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
        // L'header e i form mostrano sempre le categorie in ordine alfabetico.
        return $this->all('`nome` ASC');
    }
}
