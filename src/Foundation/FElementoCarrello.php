<?php

namespace App\Foundation;

use App\Entity\EElementoCarrello;

class FElementoCarrello extends FBaseTable
{
    protected function tableName(): string
    {
        return 'elemento_carrello';
    }

    protected function primaryKey(): string
    {
        return 'id_elemento_carrello';
    }

    protected function entityClass(): string
    {
        return EElementoCarrello::class;
    }

    protected function columns(): array
    {
        return ['id_elemento_carrello', 'id_carrello', 'id_annuncio', 'data_aggiunta'];
    }

    public function elementiAcquistabili(int $idCarrello): array
    {
        return $this->fetchEntities("
            SELECT
                e.`id_elemento_carrello`,
                e.`id_carrello`,
                e.`data_aggiunta`,
                a.*,
                c.`nome` AS categoria_nome,
                u.`username` AS venditore_username,
                ab.`id_acc_business` AS venditore_business_id,
                ab.`nome_azienda` AS venditore_nome_azienda,
                (
                    SELECT i.`url`
                    FROM `immagine` i
                    WHERE i.`id_annuncio` = a.`id_annuncio`
                    ORDER BY i.`ordine` ASC, i.`id_immagine` ASC
                    LIMIT 1
                ) AS immagine_principale
            FROM `elemento_carrello` e
            JOIN `annuncio` a ON a.`id_annuncio` = e.`id_annuncio`
            LEFT JOIN `categoria` c ON c.`id_categoria` = a.`id_categoria`
            LEFT JOIN `utente_registrato` u ON u.`id_utente` = a.`id_utente`
            LEFT JOIN `account_business` ab ON ab.`id_utente` = a.`id_utente`
            WHERE e.`id_carrello` = ?
              AND a.`stato` = 'attivo'
            ORDER BY e.`data_aggiunta` DESC
        ", [$idCarrello]);
    }

    public function activeAnnuncioIdsByUser(int $idUtente): array
    {
        $rows = $this->fetchRows("
            SELECT e.`id_annuncio`
            FROM `elemento_carrello` e
            JOIN `carrello` c ON c.`id_carrello` = e.`id_carrello`
            JOIN `annuncio` a ON a.`id_annuncio` = e.`id_annuncio`
            WHERE c.`id_utente` = ?
              AND a.`stato` = 'attivo'
        ", [$idUtente]);

        return array_map('intval', array_column($rows, 'id_annuncio'));
    }

    public function add(EElementoCarrello $elemento): void
    {
        $this->execute(
            'INSERT IGNORE INTO `elemento_carrello` (`id_carrello`, `id_annuncio`) VALUES (?, ?)',
            [$elemento->getIdCarrello(), $elemento->getIdAnnuncio()]
        );
    }

    public function remove(int $idCarrello, int $idAnnuncio): void
    {
        $this->execute(
            'DELETE FROM `elemento_carrello` WHERE `id_carrello` = ? AND `id_annuncio` = ?',
            [$idCarrello, $idAnnuncio]
        );
    }

    public function removeFromAllCarts(int $idAnnuncio): void
    {
        $this->execute('DELETE FROM `elemento_carrello` WHERE `id_annuncio` = ?', [$idAnnuncio]);
    }

    public function unavailableByCart(int $idCarrello): array
    {
        return $this->fetchRows("
            SELECT a.`id_annuncio`, a.`titolo`, a.`stato`
            FROM `elemento_carrello` e
            JOIN `annuncio` a ON a.`id_annuncio` = e.`id_annuncio`
            WHERE e.`id_carrello` = ?
              AND a.`stato` <> 'attivo'
        ", [$idCarrello]);
    }

    public function removeUnavailableByCart(int $idCarrello): void
    {
        $this->execute("
            DELETE e
            FROM `elemento_carrello` e
            JOIN `annuncio` a ON a.`id_annuncio` = e.`id_annuncio`
            WHERE e.`id_carrello` = ?
              AND a.`stato` <> 'attivo'
        ", [$idCarrello]);
    }

    public function clearCart(int $idCarrello): void
    {
        $this->execute('DELETE FROM `elemento_carrello` WHERE `id_carrello` = ?', [$idCarrello]);
    }
}
