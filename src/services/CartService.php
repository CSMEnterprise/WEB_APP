<?php

require_once __DIR__ . '/BaseService.php';

class CartService extends BaseService
{
    public function getOrCreateCartId(int $idUtente): int
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $stmt = $this->db->prepare("
            SELECT id_carrello
            FROM carrello
            WHERE id_utente = ?
            LIMIT 1
        ");
        $stmt->execute([$idUtente]);

        $cart = $stmt->fetch();

        if ($cart) {
            return (int) $cart['id_carrello'];
        }

        $stmt = $this->db->prepare("
            INSERT INTO carrello (id_utente)
            VALUES (?)
        ");
        $stmt->execute([$idUtente]);

        return $this->lastInsertId();
    }

    public function getCarrelloUtente(int $idUtente): array
    {
        $idCarrello = $this->getOrCreateCartId($idUtente);

        $stmt = $this->db->prepare("
            SELECT e.id_elemento_carrello, a.*
            FROM elemento_carrello e
            JOIN annuncio a ON a.id_annuncio = e.id_annuncio
            WHERE e.id_carrello = ?
            ORDER BY e.data_aggiunta DESC
        ");
        $stmt->execute([$idCarrello]);

        return $stmt->fetchAll();
    }

    public function getTotale(int $idUtente): float
    {
        $items = $this->getCarrelloUtente($idUtente);
        $totale = 0;

        foreach ($items as $item) {
            $totale += (float) ($item['prezzo'] ?? 0);
        }

        return $totale;
    }

    public function aggiungiAnnuncio(int $idUtente, int $idAnnuncio): void
    {
        $this->requirePositiveId($idAnnuncio, 'Annuncio');

        $idCarrello = $this->getOrCreateCartId($idUtente);

        $stmt = $this->db->prepare("
            INSERT IGNORE INTO elemento_carrello
            (id_carrello, id_annuncio)
            VALUES (?, ?)
        ");
        $stmt->execute([$idCarrello, $idAnnuncio]);
    }

    public function rimuoviAnnuncio(int $idUtente, int $idAnnuncio): void
    {
        $idCarrello = $this->getOrCreateCartId($idUtente);

        $stmt = $this->db->prepare("
            DELETE FROM elemento_carrello
            WHERE id_carrello = ? AND id_annuncio = ?
        ");
        $stmt->execute([$idCarrello, $idAnnuncio]);
    }

    public function svuota(int $idUtente): void
    {
        $idCarrello = $this->getOrCreateCartId($idUtente);

        $stmt = $this->db->prepare("
            DELETE FROM elemento_carrello
            WHERE id_carrello = ?
        ");
        $stmt->execute([$idCarrello]);
    }
}
