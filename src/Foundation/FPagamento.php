<?php

namespace App\Foundation;

use App\Entity\EPagamento;

class FPagamento extends FBaseTable
{
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
        return $this->insert([
            'id_annuncio' => $pagamento->getIdAnnuncio(),
            'id_acquirente' => $pagamento->getIdAcquirente(),
            'id_indirizzo_spedizione' => $pagamento->getIdIndirizzoSpedizione(),
            'importo_totale' => $pagamento->getImportoTotale(),
            'stato' => $pagamento->getStato(),
            'paypal_transaction_id' => $pagamento->getPaypalTransactionId(),
        ]);
    }

    public function chronologyByUser(int $idUtente): array
    {
        return $this->fetchEntities("
            SELECT
                p.*,
                a.`titolo` AS annuncio_titolo,
                a.`id_annuncio` AS annuncio_id,
                a.`id_utente` AS venditore_id,
                v.`username` AS venditore_username,
                f.`id_feedback` AS feedback_id
            FROM `pagamento` p
            JOIN `annuncio` a ON a.`id_annuncio` = p.`id_annuncio`
            LEFT JOIN `utente_registrato` v ON v.`id_utente` = a.`id_utente`
            LEFT JOIN `feedback` f ON f.`id_pagamento` = p.`id_pagamento`
                                    AND f.`id_autore` = p.`id_acquirente`
            WHERE p.`id_acquirente` = ?
            ORDER BY p.`data` DESC
        ", [$idUtente]);
    }

    public function findWithAnnuncioTitle(int $idPagamento): ?EPagamento
    {
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
        return $this->fetchEntities("
            SELECT p.*, a.`titolo`
            FROM `pagamento` p
            JOIN `annuncio` a ON a.`id_annuncio` = p.`id_annuncio`
            WHERE a.`id_utente` = ?
            ORDER BY p.`data` DESC
        ", [$idUtente]);
    }
}
