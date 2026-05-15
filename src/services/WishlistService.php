<?php

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/AnnuncioService.php';

class WishlistService extends BaseService
{
    private AnnuncioService $annuncioService;

    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->annuncioService = new AnnuncioService($db);
    }

    public function getWishlistUtente(int $idUtente): array
    {
        $this->requirePositiveId($idUtente, 'Utente');
        $this->rimuoviAnnunciNonVisibili($idUtente);

        $stmt = $this->db->prepare("
            SELECT
                p.data_aggiunta AS data_aggiunta_wishlist,
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
            FROM preferito p
            JOIN annuncio a ON a.id_annuncio = p.id_annuncio
            LEFT JOIN categoria c ON c.id_categoria = a.id_categoria
            LEFT JOIN utente_registrato u ON u.id_utente = a.id_utente
            WHERE p.id_utente = ?
              AND a.stato = 'attivo'
            ORDER BY p.data_aggiunta DESC
        ");
        $stmt->execute([$idUtente]);

        return $stmt->fetchAll();
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
            throw new ServiceException('Non puoi aggiungere alla wishlist un annuncio non disponibile.');
        }

        if ((int)($annuncio['id_utente'] ?? 0) === $idUtente) {
            throw new ServiceException('Non puoi aggiungere alla wishlist un tuo annuncio.');
        }

        $stmt = $this->db->prepare("
            INSERT IGNORE INTO preferito (id_utente, id_annuncio)
            VALUES (?, ?)
        ");
        $stmt->execute([$idUtente, $idAnnuncio]);
    }

    public function rimuoviAnnuncio(int $idUtente, int $idAnnuncio): void
    {
        $this->requirePositiveId($idUtente, 'Utente');
        $this->requirePositiveId($idAnnuncio, 'Annuncio');

        $stmt = $this->db->prepare("
            DELETE FROM preferito
            WHERE id_utente = ? AND id_annuncio = ?
        ");
        $stmt->execute([$idUtente, $idAnnuncio]);
    }

    public function svuota(int $idUtente): void
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $stmt = $this->db->prepare("
            DELETE FROM preferito
            WHERE id_utente = ?
        ");
        $stmt->execute([$idUtente]);
    }

    private function rimuoviAnnunciNonVisibili(int $idUtente): void
    {
        $stmt = $this->db->prepare("
            DELETE p
            FROM preferito p
            JOIN annuncio a ON a.id_annuncio = p.id_annuncio
            WHERE p.id_utente = ?
              AND a.stato <> 'attivo'
        ");
        $stmt->execute([$idUtente]);
    }
}
