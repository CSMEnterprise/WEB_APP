<?php

namespace App\Foundation;

use App\Entity\{
    EBaseEntity,
    EImmagine
};

/**
 * Repository delle immagini collegate agli annunci.
 */
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
        // Le immagini sono ordinate prima dal campo ordine, poi dall'id come fallback stabile.
        return $this->fetchEntities(
            'SELECT * FROM `immagine` WHERE `id_annuncio` = ? ORDER BY `ordine` ASC, `id_immagine` ASC',
            [$idAnnuncio]
        );
    }

    public function countByAnnuncio(int $idAnnuncio): int
    {
        // Usato per rispettare il limite massimo di foto per annuncio.
        return (int) $this->fetchColumn(
            'SELECT COUNT(*) FROM `immagine` WHERE `id_annuncio` = ?',
            [$idAnnuncio]
        );
    }

    public function addForAnnuncio(int $idAnnuncio, string $url, int $ordine = 0): int
    {
        // Salva solo il path pubblico; il file fisico viene gestito dal controller.
        return $this->insert([
            'id_annuncio' => $idAnnuncio,
            'url' => $url,
            'ordine' => $ordine,
        ]);
    }

    public function findOwnedByUser(int $idImmagine, int $idUtente): ?EImmagine
    {
        // La join con annuncio impedisce a un utente di cancellare immagini altrui.
        $entity = $this->fetchEntity(
            "SELECT i.*
             FROM `immagine` i
             JOIN `annuncio` a ON a.`id_annuncio` = i.`id_annuncio`
             LEFT JOIN `account_business` ab ON ab.`id_acc_business` = a.`id_business`
             WHERE i.`id_immagine` = ?
               AND (
                 a.`id_utente` = ?
                 OR ab.`id_utente` = ?
               )
               AND a.`stato` = 'attivo'
             LIMIT 1",
            [$idImmagine, $idUtente, $idUtente]
        );

        return $entity instanceof EImmagine ? $entity : null;
    }
}
