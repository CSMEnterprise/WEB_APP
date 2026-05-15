<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/AnnuncioService.php';

class CartService extends BaseService
{
    private AnnuncioService $annuncioService;

    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->annuncioService = new AnnuncioService($db);
    }

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
        $this->requirePositiveId($idUtente, 'Utente');

        $idCarrello = $this->getOrCreateCartId($idUtente);
        $this->rimuoviAnnunciNonAcquistabili($idCarrello);

        $stmt = $this->db->prepare("
            SELECT
                e.id_elemento_carrello,
                a.*,
                c.nome AS categoria_nome,
                u.username AS venditore_username,
                (
                    SELECT i.url
                    FROM immagine i
                    WHERE i.id_annuncio = a.id_annuncio
                    ORDER BY i.ordine ASC, i.id_immagine ASC
                    LIMIT 1
                ) AS immagine_principale
            FROM elemento_carrello e
            JOIN annuncio a ON a.id_annuncio = e.id_annuncio
            LEFT JOIN categoria c ON c.id_categoria = a.id_categoria
            LEFT JOIN utente_registrato u ON u.id_utente = a.id_utente
            WHERE e.id_carrello = ?
              AND a.stato = 'attivo'
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
            if ((int)($item['id_utente'] ?? 0) === $idUtente) {
                continue;
            }

            if (($item['stato'] ?? '') !== 'attivo') {
                continue;
            }

            $totale += (float) ($item['prezzo'] ?? 0);
        }

        return $totale;
    }

    public function aggiungiAnnuncio(int $idUtente, int $idAnnuncio): void
    {
        $this->requirePositiveId($idUtente, 'Utente');
        $this->requirePositiveId($idAnnuncio, 'Annuncio');

        $annuncio = $this->annuncioService->findById($idAnnuncio);

        if (!$annuncio) {
            throw new ServiceException('Annuncio non trovato.');
        }

        if (($annuncio['stato'] ?? '') !== 'attivo') {
            throw new ServiceException('Questo annuncio non è acquistabile.');
        }

        if ((int)($annuncio['id_utente'] ?? 0) === $idUtente) {
            throw new ServiceException('Non puoi aggiungere al carrello un tuo annuncio.');
        }

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

    private function rimuoviAnnunciNonAcquistabili(int $idCarrello): void
    {
        $stmt = $this->db->prepare("
            DELETE e
            FROM elemento_carrello e
            JOIN annuncio a ON a.id_annuncio = e.id_annuncio
            WHERE e.id_carrello = ?
              AND a.stato <> 'attivo'
        ");
        $stmt->execute([$idCarrello]);
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
