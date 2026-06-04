<?php

namespace App\Foundation;

use App\Entity\EPagamento;

/**
 * Repository dei pagamenti completati o simulati.
 */
class FPagamento extends FBaseTable
{
    /**
     * Metadati usati da FBaseTable per CRUD generico.
     */
    protected function tableName(): string { return 'pagamento'; }
    protected function primaryKey(): string { return 'id_pagamento'; }
    protected function entityClass(): string { return EPagamento::class; }
    protected function columns(): array
    {
        return [
            'id_pagamento',
            'id_annuncio',
            'id_acquirente',
            'id_indirizzo_spedizione',
            'importo_totale',
            'stato',
            'paypal_transaction_id',
            'data',
        ];
    }

    public function create(EPagamento $pagamento): int
    {
        // Inserisce il pagamento; la data viene lasciata al default del database.
        $row = [
            'id_annuncio' => $pagamento->getIdAnnuncio(),
            'id_acquirente' => $pagamento->getIdAcquirente(),
            'importo_totale' => $pagamento->getImportoTotale(),
            'stato' => $pagamento->getStato(),
            'paypal_transaction_id' => $pagamento->getPaypalTransactionId(),
        ];

        if ($this->hasColumn('id_indirizzo_spedizione')) {
            $row['id_indirizzo_spedizione'] = $pagamento->getIdIndirizzoSpedizione();
        }

        return $this->insert($row);
    }

    public function chronologyByUser(int $idUtente): array
    {
        // Cronologia acquisti con dati venditore e feedback gia presente, se esiste.
        return $this->fetchEntities("
            SELECT
                p.*,
                a.`titolo` AS annuncio_titolo,
                a.`id_annuncio` AS annuncio_id,
                COALESCE(a.`id_utente`, ab.`id_utente`) AS venditore_id,
                COALESCE(v.`username`, bu.`username`) AS venditore_username,
                ab.`id_acc_business` AS venditore_business_id,
                ab.`nome_azienda` AS venditore_nome_azienda,
                f.`id_feedback` AS feedback_id
            FROM `pagamento` p
            JOIN `annuncio` a ON a.`id_annuncio` = p.`id_annuncio`
            LEFT JOIN `utente_registrato` v ON v.`id_utente` = a.`id_utente`
            LEFT JOIN `account_business` ab ON ab.`id_acc_business` = a.`id_business`
            LEFT JOIN `utente_registrato` bu ON bu.`id_utente` = ab.`id_utente`
            LEFT JOIN `feedback` f ON f.`id_pagamento` = p.`id_pagamento`
                                    AND f.`id_autore` = p.`id_acquirente`
            WHERE p.`id_acquirente` = ?
            ORDER BY p.`data` DESC
        ", [$idUtente]);
    }

    public function findWithAnnuncioTitle(int $idPagamento): ?EPagamento
    {
        // Recupero puntuale usato per aprire il form feedback.
        $entity = $this->fetchEntity("
            SELECT p.*, a.`titolo`
            FROM `pagamento` p
            JOIN `annuncio` a ON a.`id_annuncio` = p.`id_annuncio`
            WHERE p.`id_pagamento` = ?
            LIMIT 1
        ", [$idPagamento]);

        return $entity instanceof EPagamento ? $entity : null;
    }

    public function receivedBySellerUser(int $idUtente): array
    {
        // Ordini ricevuti da un venditore privato sui propri annunci.
        return $this->fetchEntities("
            SELECT
                p.*,
                a.`titolo`,
                u.`username` AS acquirente_username,
                (
                    SELECT i.`url`
                    FROM `immagine` i
                    WHERE i.`id_annuncio` = a.`id_annuncio`
                    ORDER BY i.`ordine` ASC, i.`id_immagine` ASC
                    LIMIT 1
                ) AS immagine_principale
            FROM `pagamento` p
            JOIN `annuncio` a ON a.`id_annuncio` = p.`id_annuncio`
            JOIN `utente_registrato` u ON u.`id_utente` = p.`id_acquirente`
            WHERE a.`id_utente` = ?
            ORDER BY p.`data` DESC
        ", [$idUtente]);
    }

    public function receivedBySellerBusiness(int $idBusiness): array
    {
        // Ordini ricevuti da una vetrina business sui propri annunci.
        return $this->fetchEntities("
            SELECT
                p.*,
                a.`titolo`,
                u.`username` AS acquirente_username,
                (
                    SELECT i.`url`
                    FROM `immagine` i
                    WHERE i.`id_annuncio` = a.`id_annuncio`
                    ORDER BY i.`ordine` ASC, i.`id_immagine` ASC
                    LIMIT 1
                ) AS immagine_principale
            FROM `pagamento` p
            JOIN `annuncio` a ON a.`id_annuncio` = p.`id_annuncio`
            JOIN `utente_registrato` u ON u.`id_utente` = p.`id_acquirente`
            WHERE a.`id_business` = ?
            ORDER BY p.`data` DESC
        ", [$idBusiness]);
    }
}
