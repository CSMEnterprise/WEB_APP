<?php

require_once __DIR__ . '/BaseService.php';

class PaymentService extends BaseService
{
    public function create(int $buyerId, int $annuncioId, ?string $paypalTransactionId = null): int
    {
        $this->requirePositiveInt($buyerId, 'id_acquirente');
        $this->requirePositiveInt($annuncioId, 'id_annuncio');

        $annuncio = $this->fetchOne(
            "SELECT id_annuncio, prezzo, stato, id_utente
             FROM annuncio
             WHERE id_annuncio = :id
             LIMIT 1",
            [':id' => $annuncioId]
        );

        if (!$annuncio || $annuncio['stato'] !== 'attivo') {
            throw new ServiceException('Annuncio non disponibile per il pagamento.');
        }

        if ((int) $annuncio['id_utente'] === $buyerId) {
            throw new ServiceException('Non puoi acquistare un tuo annuncio.');
        }

        $this->execute(
            "INSERT INTO pagamento (id_annuncio, id_acquirente, importo_totale, stato, paypal_transaction_id)
             VALUES (:id_annuncio, :id_acquirente, :importo_totale, 'In_attesa', :paypal_transaction_id)",
            [
                ':id_annuncio' => $annuncioId,
                ':id_acquirente' => $buyerId,
                ':importo_totale' => $annuncio['prezzo'],
                ':paypal_transaction_id' => $paypalTransactionId,
            ]
        );

        return $this->lastInsertId();
    }

    public function complete(int $paymentId, ?string $paypalTransactionId = null): bool
    {
        $this->requirePositiveInt($paymentId, 'id_pagamento');

        $payment = $this->findById($paymentId);
        if (!$payment) {
            throw new ServiceException('Pagamento non trovato.');
        }

        $this->db->beginTransaction();

        try {
            $this->execute(
                "UPDATE pagamento
                 SET stato = 'Completato',
                     paypal_transaction_id = COALESCE(:paypal_transaction_id, paypal_transaction_id)
                 WHERE id_pagamento = :id",
                [
                    ':paypal_transaction_id' => $paypalTransactionId,
                    ':id' => $paymentId,
                ]
            );

            $this->execute(
                "UPDATE annuncio SET stato = 'venduto' WHERE id_annuncio = :id_annuncio",
                [':id_annuncio' => $payment['id_annuncio']]
            );

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function cancel(int $paymentId): bool
    {
        return $this->changeStatus($paymentId, 'Annullato');
    }

    public function refund(int $paymentId): bool
    {
        return $this->changeStatus($paymentId, 'Rimborsato');
    }

    public function findById(int $paymentId): ?array
    {
        $this->requirePositiveInt($paymentId, 'id_pagamento');

        return $this->fetchOne(
            'SELECT p.*, a.titolo AS annuncio_titolo, u.username AS acquirente_username
             FROM pagamento p
             INNER JOIN annuncio a ON a.id_annuncio = p.id_annuncio
             INNER JOIN utente_registrato u ON u.id_utente = p.id_acquirente
             WHERE p.id_pagamento = :id',
            [':id' => $paymentId]
        );
    }

    public function byUser(int $buyerId): array
    {
        $this->requirePositiveInt($buyerId, 'id_acquirente');

        return $this->fetchAll(
            'SELECT p.*, a.titolo AS annuncio_titolo
             FROM pagamento p
             INNER JOIN annuncio a ON a.id_annuncio = p.id_annuncio
             WHERE p.id_acquirente = :id
             ORDER BY p.data DESC',
            [':id' => $buyerId]
        );
    }

    private function changeStatus(int $paymentId, string $status): bool
    {
        $allowed = ['In_attesa', 'Completato', 'Annullato', 'Rimborsato'];
        if (!in_array($status, $allowed, true)) {
            throw new ServiceException('Stato pagamento non valido.');
        }

        return $this->execute(
            'UPDATE pagamento SET stato = :stato WHERE id_pagamento = :id',
            [
                ':stato' => $status,
                ':id' => $paymentId,
            ]
        );
    }
}
