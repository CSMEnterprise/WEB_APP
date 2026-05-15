<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/AnnuncioService.php';

class PaymentService extends BaseService
{
    private AnnuncioService $annuncioService;

    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->annuncioService = new AnnuncioService($db);
    }

    public function preparaPagamento(int $idUtente, int $idAnnuncio): array
    {
        $this->requirePositiveId($idUtente, 'Utente');
        $this->requirePositiveId($idAnnuncio, 'Annuncio');

        $annuncio = $this->annuncioService->findById($idAnnuncio);

        if (!$annuncio) {
            throw new ServiceException('Annuncio non trovato.');
        }

        if (($annuncio['stato'] ?? '') !== 'attivo') {
            throw new ServiceException('Annuncio non acquistabile.');
        }

        if ((int)($annuncio['id_utente'] ?? 0) === $idUtente) {
            throw new ServiceException('Non puoi acquistare un tuo annuncio.');
        }

        return [
            'annuncio' => $annuncio,
            'totale' => (float) $annuncio['prezzo']
        ];
    }

    public function confermaPagamento(array $data, int $idUtente): int
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $idAnnuncio = (int) ($data['id_annuncio'] ?? 0);
        $paypalTransactionId = $this->clean($data['paypal_transaction_id'] ?? '');

        $preparazione = $this->preparaPagamento($idUtente, $idAnnuncio);
        $totale = $preparazione['totale'];

        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare("
                INSERT INTO pagamento
                (id_annuncio, id_acquirente, importo_totale, stato, paypal_transaction_id)
                VALUES (?, ?, ?, 'Completato', ?)
            ");

            $stmt->execute([
                $idAnnuncio,
                $idUtente,
                $totale,
                $paypalTransactionId !== '' ? $paypalTransactionId : null
            ]);

            $idPagamento = $this->lastInsertId();

            $stmt = $this->db->prepare("
                UPDATE annuncio
                SET stato = 'venduto'
                WHERE id_annuncio = ?
            ");
            $stmt->execute([$idAnnuncio]);

            $stmt = $this->db->prepare("
                DELETE e
                FROM elemento_carrello e
                WHERE e.id_annuncio = ?
            ");
            $stmt->execute([$idAnnuncio]);

            $stmt = $this->db->prepare("
                DELETE FROM preferito
                WHERE id_annuncio = ?
            ");
            $stmt->execute([$idAnnuncio]);

            $this->db->commit();

            return $idPagamento;

        } catch (Throwable $e) {
            $this->db->rollBack();
            throw new ServiceException('Errore durante la conferma del pagamento.');
        }
    }

    public function findById(int $idPagamento): ?array
    {
        $this->requirePositiveId($idPagamento, 'Pagamento');

        $stmt = $this->db->prepare("
            SELECT p.*, a.titolo
            FROM pagamento p
            JOIN annuncio a ON a.id_annuncio = p.id_annuncio
            WHERE p.id_pagamento = ?
            LIMIT 1
        ");
        $stmt->execute([$idPagamento]);

        return $stmt->fetch() ?: null;
    }
}
