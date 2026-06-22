<?php

namespace App\Foundation;

use App\Entity\{
    EAnnuncio,
    EPreferito
};

/**
 * Repository della wishlist/preferiti degli utenti.
 */
class FPreferito extends FBaseTable
{
    protected function tableName(): string
    {
        return 'preferito';
    }

    protected function primaryKey(): string
    {
        return 'id_utente';
    }

    protected function entityClass(): string
    {
        return EPreferito::class;
    }

    protected function columns(): array
    {
        return ['id_utente', 'id_annuncio', 'data_aggiunta'];
    }

    public function annunciByUser(int $idUtente): array
    {
        // Restituisce annunci preferiti gia arricchiti con dati categoria/venditore/immagine.
        return $this->fetchEntities("
            SELECT
                p.`data_aggiunta` AS data_aggiunta_wishlist,
                a.*,
                c.`nome` AS categoria_nome,
                COALESCE(u.`username`, bu.`username`) AS venditore_username,
                COALESCE(a.`id_utente`, ab.`id_utente`) AS venditore_user_id,
                ab.`id_acc_business` AS venditore_business_id,
                ab.`nome_azienda` AS venditore_nome_azienda,
                (
                    SELECT i.`url`
                    FROM `immagine` i
                    WHERE i.`id_annuncio` = a.`id_annuncio`
                    ORDER BY i.`ordine` ASC, i.`id_immagine` ASC
                    LIMIT 1
                ) AS immagine_principale
            FROM `preferito` p
            JOIN `annuncio` a ON a.`id_annuncio` = p.`id_annuncio`
            LEFT JOIN `categoria` c ON c.`id_categoria` = a.`id_categoria`
            LEFT JOIN `utente_registrato` u ON u.`id_utente` = a.`id_utente`
            LEFT JOIN `account_business` ab ON ab.`id_acc_business` = a.`id_business`
            LEFT JOIN `utente_registrato` bu ON bu.`id_utente` = ab.`id_utente`
            WHERE p.`id_utente` = ?
              AND a.`stato` = 'attivo'
            ORDER BY p.`data_aggiunta` DESC
        ", [$idUtente]);
    }

    public function preferitiByUser(int $idUtente): array
    {
        // Lista grezza dei record preferito, utile quando non servono dati annuncio.
        return $this->fetchEntities(
            'SELECT `id_utente`, `id_annuncio`, `data_aggiunta` FROM `preferito` WHERE `id_utente` = ? ORDER BY `data_aggiunta` DESC',
            [$idUtente]
        );
    }

    public function idsByUser(int $idUtente): array
    {
        // Elenco compatto per segnare i bottoni "preferito" nella UI.
        $rows = $this->fetchRows('SELECT `id_annuncio` FROM `preferito` WHERE `id_utente` = ?', [$idUtente]);

        return array_map('intval', array_column($rows, 'id_annuncio'));
    }

    public function add(EPreferito $preferito): void
    {
        // INSERT IGNORE evita errore se l'utente aggiunge due volte lo stesso annuncio.
        $this->execute(
            'INSERT IGNORE INTO `preferito` (`id_utente`, `id_annuncio`) VALUES (?, ?)',
            [$preferito->getIdUtente(), $preferito->getIdAnnuncio()]
        );
    }

    public function remove(int $idUtente, int $idAnnuncio): void
    {
        // Rimozione puntuale da wishlist.
        $this->execute(
            'DELETE FROM `preferito` WHERE `id_utente` = ? AND `id_annuncio` = ?',
            [$idUtente, $idAnnuncio]
        );
    }

    public function removeByAnnuncio(int $idAnnuncio): void
    {
        // Usato quando un annuncio viene venduto o rimosso.
        $this->execute('DELETE FROM `preferito` WHERE `id_annuncio` = ?', [$idAnnuncio]);
    }

    public function existsForUser(int $idUtente, int $idAnnuncio): bool
    {
        // Supporta il toggle preferito senza caricare tutta la wishlist.
        return (bool) $this->fetchColumn(
            'SELECT 1 FROM `preferito` WHERE `id_utente` = ? AND `id_annuncio` = ? LIMIT 1',
            [$idUtente, $idAnnuncio]
        );
    }

    public function clearForUser(int $idUtente): void
    {
        // Svuota la wishlist dell'utente.
        $this->execute('DELETE FROM `preferito` WHERE `id_utente` = ?', [$idUtente]);
    }

    public function removeUnavailableForUser(int $idUtente): void
    {
        // Pulisce preferiti collegati ad annunci non piu attivi.
        $this->execute("
            DELETE p
            FROM `preferito` p
            JOIN `annuncio` a ON a.`id_annuncio` = p.`id_annuncio`
            WHERE p.`id_utente` = ?
              AND a.`stato` <> 'attivo'
        ", [$idUtente]);
    }
}
