<?php

namespace App\Foundation;

use App\Entity\ECarrello;

class FCarrello extends FBaseTable
{
    protected function tableName(): string
    {
        return 'carrello';
    }

    protected function primaryKey(): string
    {
        return 'id_carrello';
    }

    protected function entityClass(): string
    {
        return ECarrello::class;
    }

    protected function columns(): array
    {
        return ['id_carrello', 'id_utente', 'data_creazione', 'data_aggiornamento'];
    }

    public function findByUser(int $idUtente): ?ECarrello
    {
        $entity = $this->fetchEntity(
            'SELECT * FROM `carrello` WHERE `id_utente` = ? LIMIT 1',
            [$idUtente]
        );

        return $entity instanceof ECarrello ? $entity : null;
    }

    public function getOrCreateIdByUser(int $idUtente): int
    {
        $carrello = $this->findByUser($idUtente);

        if ($carrello) {
            return (int) $carrello->getIdCarrello();
        }

        return $this->insert(['id_utente' => $idUtente]);
    }

    public function findCart(int $idCarrello): ?ECarrello
    {
        $entity = $this->find($idCarrello);

        return $entity instanceof ECarrello ? $entity : null;
    }
}
