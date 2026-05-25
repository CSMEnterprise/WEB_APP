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

class CartService extends BaseService
{
    private AnnuncioService $annuncioService;
    private array $ultimiAnnunciRimossi = [];

    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->annuncioService = new AnnuncioService($db);
    }

    public function getOrCreateCartEntity(int $idUtente): ECarrello
    {
        $idCarrello = $this->getOrCreateCartId($idUtente);
        $carrello = $this->findCartEntityById($idCarrello);
        $carrello->setElementi($this->getCarrelloUtenteEntity($idUtente));

        return $carrello;
    }

    public function getOrCreateCartId(int $idUtente): int
    {
        $this->requirePositiveId($idUtente, 'Utente');
        $this->denyBusinessBuyer($idUtente);

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
        $this->denyBusinessBuyer($idUtente);

        $idCarrello = $this->getOrCreateCartId($idUtente);
        $this->rimuoviAnnunciNonAcquistabili($idCarrello);

        $stmt = $this->db->prepare("
            SELECT
                e.id_elemento_carrello,
                e.id_carrello,
                e.data_aggiunta,
                a.*,
                c.nome AS categoria_nome,
                u.username AS venditore_username,
                ab.id_acc_business AS venditore_business_id,
                ab.nome_azienda AS venditore_nome_azienda,
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
            LEFT JOIN account_business ab ON ab.id_utente = a.id_utente
            WHERE e.id_carrello = ?
              AND a.stato = 'attivo'
            ORDER BY e.data_aggiunta DESC
        ");
        $stmt->execute([$idCarrello]);

        return $stmt->fetchAll();
    }

    public function getCarrelloUtenteEntity(int $idUtente): array
    {
        return $this->toElementoCarrelloEntities($this->getCarrelloUtente($idUtente));
    }

    public function getUltimiAnnunciRimossi(): array
    {
        return $this->ultimiAnnunciRimossi;
    }

    public function getTotale(int $idUtente): float
    {
        $items = $this->getCarrelloUtente($idUtente);
        $totale = 0;

        foreach ($items as $item) {
            $annuncio = EAnnuncio::fromArray($item);

            if ((int)($annuncio->getIdUtente() ?? 0) === $idUtente) {
                continue;
            }

            if (!$annuncio->isAttivo()) {
                continue;
            }

            $totale += $annuncio->getPrezzo();
        }

        return $totale;
    }

    public function getCarrelloIds(int $idUtente): array
    {
        $this->requirePositiveId($idUtente, 'Utente');

        $stmt = $this->db->prepare("
            SELECT e.id_annuncio
            FROM elemento_carrello e
            JOIN carrello c ON c.id_carrello = e.id_carrello
            JOIN annuncio a ON a.id_annuncio = e.id_annuncio
            WHERE c.id_utente = ?
              AND a.stato = 'attivo'
        ");
        $stmt->execute([$idUtente]);

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    public function aggiungiAnnuncio(int $idUtente, int $idAnnuncio): void
    {
        $this->requirePositiveId($idUtente, 'Utente');
        $this->requirePositiveId($idAnnuncio, 'Annuncio');
        $this->denyBusinessBuyer($idUtente);

        $annuncio = $this->annuncioService->findEntityById($idAnnuncio);

        if (!$annuncio) {
            throw new ServiceException('Annuncio non trovato.');
        }

        if (!$annuncio->isAttivo()) {
            throw new ServiceException('Questo annuncio non è acquistabile.');
        }

        if ((int)($annuncio->getIdUtente() ?? 0) === $idUtente) {
            throw new ServiceException('Non puoi aggiungere al carrello un tuo annuncio.');
        }

        $idCarrello = $this->getOrCreateCartId($idUtente);
        $elemento = new EElementoCarrello($idCarrello, $idAnnuncio);

        $stmt = $this->db->prepare("
            INSERT IGNORE INTO elemento_carrello
            (id_carrello, id_annuncio)
            VALUES (?, ?)
        ");
        $stmt->execute([$elemento->getIdCarrello(), $elemento->getIdAnnuncio()]);

        // Se l'annuncio era nella wishlist, lo rimuoviamo automaticamente:
        // quando un prodotto passa al carrello non deve restare anche tra i preferiti.
        $stmt = $this->db->prepare("
            DELETE FROM preferito
            WHERE id_utente = ? AND id_annuncio = ?
        ");
        $stmt->execute([$idUtente, $idAnnuncio]);
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

    public function rimuoviAnnuncioDaTuttiICarrelli(int $idAnnuncio): void
    {
        $this->requirePositiveId($idAnnuncio, 'Annuncio');

        $stmt = $this->db->prepare("
            DELETE FROM elemento_carrello
            WHERE id_annuncio = ?
        ");
        $stmt->execute([$idAnnuncio]);
    }

    private function rimuoviAnnunciNonAcquistabili(int $idCarrello): void
    {
        $stmt = $this->db->prepare("
            SELECT a.id_annuncio, a.titolo, a.stato
            FROM elemento_carrello e
            JOIN annuncio a ON a.id_annuncio = e.id_annuncio
            WHERE e.id_carrello = ?
              AND a.stato <> 'attivo'
        ");
        $stmt->execute([$idCarrello]);
        $this->ultimiAnnunciRimossi = $stmt->fetchAll();

        if (empty($this->ultimiAnnunciRimossi)) {
            return;
        }

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

    private function findCartEntityById(int $idCarrello): ECarrello
    {
        $this->requirePositiveId($idCarrello, 'Carrello');

        $stmt = $this->db->prepare("
            SELECT *
            FROM carrello
            WHERE id_carrello = ?
            LIMIT 1
        ");
        $stmt->execute([$idCarrello]);

        $carrello = $stmt->fetch();

        if (!$carrello) {
            throw new ServiceException('Carrello non trovato.');
        }

        return ECarrello::fromArray($carrello);
    }

    private function toElementoCarrelloEntities(array $elementi): array
    {
        return array_map(static fn(array $elemento) => EElementoCarrello::fromArray($elemento), $elementi);
    }
}
