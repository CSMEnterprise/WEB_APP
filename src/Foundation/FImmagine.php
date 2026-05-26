<?php

namespace App\Foundation;

use App\Entity\EBaseEntity;
use App\Entity\EImmagine;

class FImmagine extends FBaseTable
{
    protected function tableName(): string
    {
        return 'immagine';
    }

    protected function primaryKey(): string
    {
        return 'id_immagine';
    }

    protected function entityClass(): string
    {
        return EImmagine::class;
    }

    protected function columns(): array
    {
        return ['id_immagine', 'id_annuncio', 'url', 'ordine'];
    }

    public function byAnnuncio(int $idAnnuncio): array
    {
        return $this->fetchEntities(
            'SELECT * FROM `immagine` WHERE `id_annuncio` = ? ORDER BY `ordine` ASC, `id_immagine` ASC',
            [$idAnnuncio]
        );
    }

    public function countByAnnuncio(int $idAnnuncio): int
    {
        return (int) $this->fetchColumn(
            'SELECT COUNT(*) FROM `immagine` WHERE `id_annuncio` = ?',
            [$idAnnuncio]
        );
    }

    public function addForAnnuncio(int $idAnnuncio, string $url, int $ordine = 0): int
    {
        return $this->insert([
            'id_annuncio' => $idAnnuncio,
            'url' => $url,
            'ordine' => $ordine,
        ]);
    }

    public function findOwnedByUser(int $idImmagine, int $idUtente): ?EImmagine
    {
        $entity = $this->fetchEntity(
            "SELECT i.*
             FROM `immagine` i
             JOIN `annuncio` a ON a.`id_annuncio` = i.`id_annuncio`
             WHERE i.`id_immagine` = ?
               AND a.`id_utente` = ?
               AND a.`stato` = 'attivo'
             LIMIT 1",
            [$idImmagine, $idUtente]
        );

        return $entity instanceof EImmagine ? $entity : null;
    }
}
