<?php

require_once __DIR__ . '/BaseService.php';

class FeedbackService extends BaseService
{
    public function create(int $authorId, int $paymentId, int $rating, ?string $comment = null): int
    {
        $this->requirePositiveInt($authorId, 'id_autore');
        $this->requirePositiveInt($paymentId, 'id_pagamento');

        if ($rating < 1 || $rating > 5) {
            throw new ServiceException('La valutazione deve essere compresa tra 1 e 5.');
        }

        $payment = $this->fetchOne(
            "SELECT id_pagamento, id_acquirente, stato FROM pagamento WHERE id_pagamento = :id LIMIT 1",
            [':id' => $paymentId]
        );

        if (!$payment || $payment['stato'] !== 'Completato') {
            throw new ServiceException('Puoi lasciare un feedback solo dopo un pagamento completato.');
        }

        if ((int) $payment['id_acquirente'] !== $authorId) {
            throw new ServiceException('Puoi lasciare feedback solo per i tuoi acquisti.');
        }

        $this->execute(
            'INSERT INTO feedback (id_autore, id_pagamento, valutazione, commento)
             VALUES (:id_autore, :id_pagamento, :valutazione, :commento)',
            [
                ':id_autore' => $authorId,
                ':id_pagamento' => $paymentId,
                ':valutazione' => $rating,
                ':commento' => trim((string) $comment) ?: null,
            ]
        );

        return $this->lastInsertId();
    }

    public function byAnnuncio(int $annuncioId): array
    {
        $this->requirePositiveInt($annuncioId, 'id_annuncio');

        return $this->fetchAll(
            'SELECT f.*, u.username AS autore_username
             FROM feedback f
             INNER JOIN pagamento p ON p.id_pagamento = f.id_pagamento
             INNER JOIN utente_registrato u ON u.id_utente = f.id_autore
             WHERE p.id_annuncio = :id_annuncio
             ORDER BY f.data_feedback DESC',
            [':id_annuncio' => $annuncioId]
        );
    }

    public function delete(int $feedbackId): bool
    {
        $this->requirePositiveInt($feedbackId, 'id_feedback');

        return $this->execute(
            'DELETE FROM feedback WHERE id_feedback = :id',
            [':id' => $feedbackId]
        );
    }
}
