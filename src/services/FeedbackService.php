<?php

namespace App\Services;

use App\Entity\EAccountBusiness;
use App\Entity\EAdmin;
use App\Entity\EAnnuncio;
use App\Entity\ECarrello;
use App\Entity\ECategoria;
use App\Entity\EElementoCarrello;
use App\Entity\EFeedback;
use App\Entity\EImmagine;
use App\Entity\EIndirizzo;
use App\Entity\EModera;
use App\Entity\EPagamento;
use App\Entity\EPreferito;
use App\Entity\ESegnalazione;
use Exception;
use PDO;
use PDOException;
use Throwable;
use finfo;

class FeedbackService extends BaseService
{
    public function crea(array $data, int $idAutore): int
    {
        $feedback = EFeedback::fromArray(array_merge($data, [
            'id_autore' => $idAutore,
            'valutazione' => (int) ($data['valutazione'] ?? $data['voto'] ?? 0),
        ]));

        return $this->creaDaEntity($feedback);
    }

    public function creaDaEntity(EFeedback $feedback): int
    {
        $idAutore = $feedback->getIdAutore();
        $idPagamento = $feedback->getIdPagamento();
        $valutazione = $feedback->getValutazione();
        $commento = $this->clean($feedback->getCommento());

        $this->requirePositiveId($idAutore, 'Autore');

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

    public function getByPagamentoIdEntity(int $idPagamento): array
    {
        return $this->toFeedbackEntities($this->getByPagamentoId($idPagamento));
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

    public function getByUserIdEntity(int $idUtente): array
    {
        return $this->toFeedbackEntities($this->getByUserId($idUtente));
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

    public function getByVenditoreIdEntity(int $idVenditore): array
    {
        return $this->toFeedbackEntities($this->getByVenditoreId($idVenditore));
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

    private function toFeedbackEntities(array $feedbackList): array
    {
        return array_map(static fn(array $feedback) => EFeedback::fromArray($feedback), $feedbackList);
    }
}
