<?php

namespace App\Foundation;

use App\Entity\EIndirizzo;

class FIndirizzo extends FBaseTable
{
    protected function tableName(): string
    {
        return 'indirizzi';
    }

    protected function primaryKey(): string
    {
        return 'id_indirizzo';
    }

    protected function entityClass(): string
    {
        return EIndirizzo::class;
    }

    protected function columns(): array
    {
        return [
            'id_indirizzo',
            'id_utente',
            'id_business',
            'tipo',
            'via',
            'numero',
            'cap',
            'citta',
            'provincia',
            'paese',
            'predefinito',
        ];
    }

    public function countByUser(int $idUtente): int
    {
        return (int) $this->fetchColumn(
            'SELECT COUNT(*) FROM `indirizzi` WHERE `id_utente` = ?',
            [$idUtente]
        );
    }

    public function createForUser(EIndirizzo $indirizzo): int
    {
        $row = $indirizzo->toArray();
        unset($row['id_indirizzo']);

        return $this->insert($row);
    }

    public function createForBusiness(EIndirizzo $indirizzo): int
    {
        $row = $indirizzo->toArray();
        unset($row['id_indirizzo']);

        return $this->insert($row);
    }

    public function byUser(int $idUtente): array
    {
        return $this->fetchEntities(
            'SELECT * FROM `indirizzi` WHERE `id_utente` = ? ORDER BY `predefinito` DESC, `id_indirizzo` DESC',
            [$idUtente]
        );
    }

    public function findForUser(int $idIndirizzo, int $idUtente): ?EIndirizzo
    {
        $entity = $this->fetchEntity(
            'SELECT * FROM `indirizzi` WHERE `id_indirizzo` = ? AND `id_utente` = ? LIMIT 1',
            [$idIndirizzo, $idUtente]
        );

        return $entity instanceof EIndirizzo ? $entity : null;
    }

    public function setDefaultForUser(int $idUtente, int $idIndirizzo): void
    {
        $this->execute('UPDATE `indirizzi` SET `predefinito` = 0 WHERE `id_utente` = ?', [$idUtente]);
        $this->execute(
            'UPDATE `indirizzi` SET `predefinito` = 1 WHERE `id_indirizzo` = ? AND `id_utente` = ?',
            [$idIndirizzo, $idUtente]
        );
    }

    public function updateForUser(EIndirizzo $indirizzo): void
    {
        $this->execute("
            UPDATE `indirizzi`
            SET `via` = ?, `numero` = ?, `cap` = ?, `citta` = ?, `provincia` = ?, `paese` = ?
            WHERE `id_indirizzo` = ? AND `id_utente` = ?
        ", [
            $indirizzo->getVia(),
            $indirizzo->getNumero(),
            $indirizzo->getCap(),
            $indirizzo->getCitta(),
            $indirizzo->getProvincia(),
            $indirizzo->getPaese(),
            $indirizzo->getIdIndirizzo(),
            $indirizzo->getIdUtente(),
        ]);
    }

    public function deleteForUser(int $idIndirizzo, int $idUtente): void
    {
        $this->execute(
            'DELETE FROM `indirizzi` WHERE `id_indirizzo` = ? AND `id_utente` = ?',
            [$idIndirizzo, $idUtente]
        );
    }

    public function makeMostRecentDefault(int $idUtente): void
    {
        $this->execute("
            UPDATE `indirizzi` SET `predefinito` = 1
            WHERE `id_utente` = ?
            ORDER BY `id_indirizzo` DESC
            LIMIT 1
        ", [$idUtente]);
    }

    public function deleteDefaultForBusiness(int $idBusiness): void
    {
        $this->execute(
            'DELETE FROM `indirizzi` WHERE `id_business` = ? AND `predefinito` = 1',
            [$idBusiness]
        );
    }
}
