<?php

namespace App\Controllers;

use App\Entity\EAnnuncio;
use App\Entity\EPreferito;
use App\Foundation\FDataBase;
use App\Foundation\FPersistentManager;
use App\Services\ServiceException;
use Exception;
use PDO;

/**
 * Gestisce la wishlist dell'utente e le azioni rapide dai dettagli/lista annunci.
 */
class WishlistController extends BaseController
{
    /**
     * Inizializza il layer persistence con la connessione corrente.
     */
    public function __construct(PDO $db)
    {
        FDataBase::init($db);
    }

    /**
     * Mostra la wishlist pulendo prima eventuali annunci non piu disponibili.
     */
    public function lista(int $idUtente): void
    {
        try {
            $this->requirePositiveId($idUtente, 'Utente');
            $this->denyBusinessBuyer($idUtente);

            FPersistentManager::removeUnavailablePreferitiForUser($idUtente);
            $wishlist = $this->entitiesToArrays(FPersistentManager::wishlistAnnunciByUser($idUtente));
            $wishlistIds = array_map(
                static fn(array $annuncio): int => (int) ($annuncio['id_annuncio'] ?? 0),
                $wishlist
            );

            $this->view('wishlist/lista.tpl', compact('wishlist', 'wishlistIds'), 'Wishlist');
        } catch (Exception $e) {
            $this->renderError($e->getMessage(), 400);
        }
    }

    /**
     * Aggiunge un annuncio alla wishlist e apre la pagina preferiti.
     */
    public function aggiungi(int $idUtente, int $idAnnuncio): void
    {
        try {
            $this->aggiungiAnnuncioAllaWishlist($idUtente, $idAnnuncio);

            header('Location: index.php?route=wishlist');
            exit;
        } catch (Exception $e) {
            $this->renderError($e->getMessage(), 400);
        }
    }

    /**
     * Rimuove un annuncio dalla wishlist.
     */
    public function rimuovi(int $idUtente, int $idAnnuncio): void
    {
        try {
            $this->requirePositiveId($idUtente, 'Utente');
            $this->requirePositiveId($idAnnuncio, 'Annuncio');
            $this->denyBusinessBuyer($idUtente);

            FPersistentManager::removePreferito($idUtente, $idAnnuncio);

            header('Location: index.php?route=wishlist');
            exit;
        } catch (Exception $e) {
            $this->renderError($e->getMessage(), 400);
        }
    }

    /**
     * Aggiunge o rimuove il preferito mantenendo l'utente sulla pagina di provenienza.
     */
    public function toggle(int $idUtente, int $idAnnuncio): void
    {
        try {
            $this->requirePositiveId($idUtente, 'Utente');
            $this->requirePositiveId($idAnnuncio, 'Annuncio');
            $this->denyBusinessBuyer($idUtente);

            if (FPersistentManager::preferitoExists($idUtente, $idAnnuncio)) {
                FPersistentManager::removePreferito($idUtente, $idAnnuncio);
            } else {
                $this->aggiungiAnnuncioAllaWishlist($idUtente, $idAnnuncio);
            }

            $redirect = $_SERVER['HTTP_REFERER'] ?? 'index.php?route=annunci';
            header('Location: ' . $redirect);
            exit;
        } catch (Exception $e) {
            $this->renderError($e->getMessage(), 400);
        }
    }

    /**
     * Svuota tutti i preferiti dell'utente.
     */
    public function svuota(int $idUtente): void
    {
        try {
            $this->requirePositiveId($idUtente, 'Utente');
            $this->denyBusinessBuyer($idUtente);

            FPersistentManager::clearPreferitiForUser($idUtente);

            header('Location: index.php?route=wishlist');
            exit;
        } catch (Exception $e) {
            $this->renderError($e->getMessage(), 400);
        }
    }

    /**
     * Applica le regole per evitare preferiti non validi o propri annunci.
     */
    private function aggiungiAnnuncioAllaWishlist(int $idUtente, int $idAnnuncio): void
    {
        $this->requirePositiveId($idUtente, 'Utente');
        $this->requirePositiveId($idAnnuncio, 'Annuncio');
        $this->denyBusinessBuyer($idUtente);

        $annuncio = FPersistentManager::annuncioById($idAnnuncio);

        if (!$annuncio instanceof EAnnuncio) {
            throw new ServiceException('Annuncio non trovato.');
        }

        if (!$annuncio->isAttivo()) {
            throw new ServiceException('Non puoi aggiungere alla wishlist un annuncio non disponibile.');
        }

        if ((int)($annuncio->getIdUtente() ?? 0) === $idUtente) {
            throw new ServiceException('Non puoi aggiungere alla wishlist un tuo annuncio.');
        }

        FPersistentManager::addPreferito(new EPreferito($idUtente, $idAnnuncio));
    }
}
