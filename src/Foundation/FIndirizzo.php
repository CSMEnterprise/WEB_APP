<?php

namespace App\Foundation;

use App\Entity\EIndirizzo;

/**
 * Repository degli indirizzi di spedizione utente e delle sedi business.
 */
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
        // Usato per capire se il nuovo indirizzo deve diventare predefinito.
        return (int) $this->fetchColumn(
            'SELECT COUNT(*) FROM `indirizzi` WHERE `id_utente` = ?',
            [$idUtente]
        );
    }

    public function createForUser(EIndirizzo $indirizzo): int
    {
        // L'id e autoincrement, quindi viene rimosso prima dell'inserimento.
        $row = $indirizzo->toArray();
        unset($row['id_indirizzo']);

        return $this->insert($row);
    }

    public function createForBusiness(EIndirizzo $indirizzo): int
    {
        // Stessa tabella degli indirizzi utente, ma popolata tramite id_business.
        $row = $indirizzo->toArray();
        unset($row['id_indirizzo']);

        return $this->insert($row);
    }

    public function byUser(int $idUtente): array
    {
        // Mostra prima il predefinito, poi i piu recenti.
        return $this->fetchEntities(
            'SELECT * FROM `indirizzi` WHERE `id_utente` = ? ORDER BY `predefinito` DESC, `id_indirizzo` DESC',
            [$idUtente]
        );
    }

    public function findForUser(int $idIndirizzo, int $idUtente): ?EIndirizzo
    {
        // Vincola sempre l'indirizzo all'utente per evitare accessi incrociati.
        $entity = $this->fetchEntity(
            'SELECT * FROM `indirizzi` WHERE `id_indirizzo` = ? AND `id_utente` = ? LIMIT 1',
            [$idIndirizzo, $idUtente]
        );

        return $entity instanceof EIndirizzo ? $entity : null;
    }

    public function setDefaultForUser(int $idUtente, int $idIndirizzo): void
    {
        // Prima azzera tutti i predefiniti, poi assegna quello scelto.
        $this->execute('UPDATE `indirizzi` SET `predefinito` = 0 WHERE `id_utente` = ?', [$idUtente]);
        $this->execute(
            'UPDATE `indirizzi` SET `predefinito` = 1 WHERE `id_indirizzo` = ? AND `id_utente` = ?',
            [$idIndirizzo, $idUtente]
        );
    }

    public function updateForUser(EIndirizzo $indirizzo): void
    {
        // Aggiorna solo campi indirizzo, non cambia proprietario o stato predefinito.
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
        // Cancella solo se l'indirizzo appartiene all'utente.
        $this->execute(
            'DELETE FROM `indirizzi` WHERE `id_indirizzo` = ? AND `id_utente` = ?',
            [$idIndirizzo, $idUtente]
        );
    }

    public function makeMostRecentDefault(int $idUtente): void
    {
        // Dopo cancellazione del predefinito promuove l'indirizzo piu recente rimasto.
        $this->execute("
            UPDATE `indirizzi` SET `predefinito` = 1
            WHERE `id_utente` = ?
            ORDER BY `id_indirizzo` DESC
            LIMIT 1
        ", [$idUtente]);
    }

    public function deleteDefaultForBusiness(int $idBusiness): void
    {
        // La sede business viene sostituita eliminando quella predefinita corrente.
        $this->execute(
            'DELETE FROM `indirizzi` WHERE `id_business` = ? AND `predefinito` = 1',
            [$idBusiness]
        );
    }
}
