<?php

namespace App\Foundation;

use App\Entity\ECarrello;

/**
 * Repository del carrello: un utente normale ha un solo carrello attivo.
 */
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
        // Cerca il carrello associato all'utente, se e gia stato creato.
        $entity = $this->fetchEntity(
            'SELECT * FROM `carrello` WHERE `id_utente` = ? LIMIT 1',
            [$idUtente]
        );

        return $entity instanceof ECarrello ? $entity : null;
    }

    public function getOrCreateIdByUser(int $idUtente): int
    {
        // Pattern get-or-create: evita di creare carrelli duplicati a ogni visita.
        $carrello = $this->findByUser($idUtente);

        if ($carrello) {
            return (int) $carrello->getIdCarrello();
        }

        return $this->insert(['id_utente' => $idUtente]);
    }

    public function findCart(int $idCarrello): ?ECarrello
    {
        // Wrapper tipizzato attorno al find generico della classe base.
        $entity = $this->find($idCarrello);

        return $entity instanceof ECarrello ? $entity : null;
    }

    public function countActiveItemsByUser(int $idUtente): int
    {
        return (int) $this->fetchColumn(
            "SELECT COUNT(*)
             FROM `carrello` c
             JOIN `elemento_carrello` e ON e.`id_carrello` = c.`id_carrello`
             JOIN `annuncio` a ON a.`id_annuncio` = e.`id_annuncio`
             WHERE c.`id_utente` = ? AND a.`stato` = 'attivo'",
            [$idUtente]
        );
    }
}
