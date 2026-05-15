<?php

require_once __DIR__ . '/BaseService.php';

class FeedbackService extends BaseService
{
    public function crea(array $data, int $idAutore): int
    {
        $this->requirePositiveId($idAutore, 'Autore');

        $idPagamento = (int) ($data['id_pagamento'] ?? 0);
        $valutazione = (int) ($data['valutazione'] ?? $data['voto'] ?? 0);
        $commento = $this->clean($data['commento'] ?? '');

        if ($idPagamento <= 0) {
            throw new ServiceException('Pagamento obbligatorio.');
        }

        if ($valutazione < 1 || $valutazione > 5) {
            throw new ServiceException('La valutazione deve essere compresa tra 1 e 5.');
        }

        $stmt = $this->db->prepare("
            INSERT INTO feedback
            (id_autore, id_pagamento, valutazione, commento)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $idAutore,
            $idPagamento,
            $valutazione,
            $commento !== '' ? $commento : null
        ]);

        return $this->lastInsertId();
    }

    public function getByPagamentoId(int $idPagamento): array
    {
        $this->requirePositiveId($idPagamento, 'Pagamento');

        $stmt = $this->db->prepare("
            SELECT f.*, u.username AS autore
            FROM feedback f
            JOIN utente_registrato u ON u.id_utente = f.id_autore
            WHERE f.id_pagamento = ?
            ORDER BY f.data_feedback DESC
        ");
        $stmt->execute([$idPagamento]);

        return $stmt->fetchAll();
    }

    public function getByUserId(int $idUtente): array
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $stmt = $this->db->prepare("
            SELECT f.*, u.username AS autore, p.id_acquirente, a.id_utente AS venditore_id, a.titolo
            FROM feedback f
            JOIN utente_registrato u ON u.id_utente = f.id_autore
            JOIN pagamento p ON p.id_pagamento = f.id_pagamento
            JOIN annuncio a ON a.id_annuncio = p.id_annuncio
            WHERE p.id_acquirente = ? OR a.id_utente = ?
            ORDER BY f.data_feedback DESC
        ");
        $stmt->execute([$idUtente, $idUtente]);

        return $stmt->fetchAll();
    }

    public function hasFeedback(int $idPagamento, int $idAutore): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM feedback
            WHERE id_pagamento = ? AND id_autore = ?
        ");
        $stmt->execute([$idPagamento, $idAutore]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function getByVenditoreId(int $idVenditore): array
    {
        $this->requirePositiveId($idVenditore, 'Venditore');

        $stmt = $this->db->prepare("
            SELECT
                f.*,
                u.username  AS autore,
                a.titolo    AS annuncio_titolo,
                a.id_annuncio AS annuncio_id
            FROM feedback f
            JOIN utente_registrato u ON u.id_utente   = f.id_autore
            JOIN pagamento p         ON p.id_pagamento = f.id_pagamento
            JOIN annuncio a          ON a.id_annuncio  = p.id_annuncio
            WHERE a.id_utente = ?
            ORDER BY f.data_feedback DESC
        ");
        $stmt->execute([$idVenditore]);
        return $stmt->fetchAll();
    }

    public function getMediaVoto(int $idUtente): float
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $stmt = $this->db->prepare("
            SELECT AVG(f.valutazione)
            FROM feedback f
            JOIN pagamento p ON p.id_pagamento = f.id_pagamento
            JOIN annuncio a ON a.id_annuncio = p.id_annuncio
            WHERE a.id_utente = ?
        ");
        $stmt->execute([$idUtente]);

        return (float) $stmt->fetchColumn();
    }
}
