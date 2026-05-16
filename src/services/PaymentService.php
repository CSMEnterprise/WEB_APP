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
        $this->denyBusinessBuyer($idUtente);

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
        $this->denyBusinessBuyer($idUtente);

        $idAnnuncio = (int) ($data['id_annuncio'] ?? 0);
        $this->requirePositiveId($idAnnuncio, 'Annuncio');

        $idIndirizzo = (int) ($data['id_indirizzo'] ?? 0);
        $this->requirePositiveId($idIndirizzo, 'Indirizzo di spedizione');

        $paypalTransactionId = $this->clean($data['paypal_transaction_id'] ?? '');

        $this->db->beginTransaction();

        try {
            $annuncio = $this->getAnnuncioForPaymentUpdate($idAnnuncio);

            if (!$annuncio) {
                throw new ServiceException('Annuncio non trovato.');
            }

            if (($annuncio['stato'] ?? '') !== 'attivo') {
                throw new ServiceException('Annuncio non acquistabile.');
            }

            if ((int)($annuncio['id_utente'] ?? 0) === $idUtente) {
                throw new ServiceException('Non puoi acquistare un tuo annuncio.');
            }

            if (!$this->indirizzoBelongsToUser($idIndirizzo, $idUtente)) {
                throw new ServiceException('Indirizzo di spedizione non valido.');
            }

            $totale = (float) $annuncio['prezzo'];

            $stmt = $this->db->prepare("
                INSERT INTO pagamento
                (id_annuncio, id_acquirente, id_indirizzo_spedizione, importo_totale, stato, paypal_transaction_id)
                VALUES (?, ?, ?, ?, 'Completato', ?)
            ");

            $stmt->execute([
                $idAnnuncio,
                $idUtente,
                $idIndirizzo,
                $totale,
                $paypalTransactionId !== '' ? $paypalTransactionId : null
            ]);

            $idPagamento = $this->lastInsertId();

            $stmt = $this->db->prepare("
                UPDATE annuncio
                SET stato = 'venduto'
                WHERE id_annuncio = ? AND stato = 'attivo'
            ");
            $stmt->execute([$idAnnuncio]);

            if ($stmt->rowCount() !== 1) {
                throw new ServiceException('Annuncio non acquistabile.');
            }

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
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            if ($e instanceof ServiceException) {
                throw $e;
            }

            throw new ServiceException('Errore durante la conferma del pagamento.');
        }
    }

    private function getAnnuncioForPaymentUpdate(int $idAnnuncio): ?array
    {
        $stmt = $this->db->prepare("
            SELECT
                a.*,
                c.nome AS categoria_nome,
                u.username AS venditore_username,
                ab.id_acc_business AS venditore_business_id,
                ab.nome_azienda AS venditore_nome_azienda
            FROM annuncio a
            LEFT JOIN categoria c ON c.id_categoria = a.id_categoria
            LEFT JOIN utente_registrato u ON u.id_utente = a.id_utente
            LEFT JOIN account_business ab ON ab.id_utente = a.id_utente
            WHERE a.id_annuncio = ?
            LIMIT 1
            FOR UPDATE
        ");
        $stmt->execute([$idAnnuncio]);

        return $stmt->fetch() ?: null;
    }

    private function indirizzoBelongsToUser(int $idIndirizzo, int $idUtente): bool
    {
        $stmt = $this->db->prepare("
            SELECT 1
            FROM indirizzi
            WHERE id_indirizzo = ? AND id_utente = ?
            LIMIT 1
        ");
        $stmt->execute([$idIndirizzo, $idUtente]);

        return (bool) $stmt->fetchColumn();
    }

    public function getCronologiaByUserId(int $idUtente): array
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $stmt = $this->db->prepare("
            SELECT
                p.*,
                a.titolo              AS annuncio_titolo,
                a.id_annuncio         AS annuncio_id,
                a.id_utente           AS venditore_id,
                v.username            AS venditore_username,
                f.id_feedback         AS feedback_id
            FROM pagamento p
            JOIN annuncio a               ON a.id_annuncio  = p.id_annuncio
            LEFT JOIN utente_registrato v ON v.id_utente    = a.id_utente
            LEFT JOIN feedback f          ON f.id_pagamento = p.id_pagamento
                                        AND f.id_autore    = p.id_acquirente
            WHERE p.id_acquirente = ?
            ORDER BY p.data DESC
        ");
        $stmt->execute([$idUtente]);

        return $stmt->fetchAll();
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
