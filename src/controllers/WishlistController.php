<?php

namespace App\Controllers;

use App\Entity\EAnnuncio;
use App\Entity\EPreferito;
use App\Foundation\FDataBase;
use App\Foundation\FPersistentManager;
use App\Services\ServiceException;
use Exception;
use PDO;

class WishlistController extends BaseController
{
    public function __construct(PDO $db)
    {
        FDataBase::init($db);
    }

    public function lista(int $idUtente): void
    {
        try {
            $this->requirePositiveId($idUtente, 'Utente');
            $this->denyBusinessBuyer($idUtente);

            FPersistentManager::removeUnavailablePreferitiForUser($idUtente);
            $wishlist = $this->entitiesToArrays(FPersistentManager::wishlistAnnunciByUser($idUtente));

            require __DIR__ . '/../views/wishlist/lista.php';
        } catch (Exception $e) {
            http_response_code(400);
            $errore = $e->getMessage();
            require __DIR__ . '/../views/errors/400.php';
        }
    }

    public function aggiungi(int $idUtente, int $idAnnuncio): void
    {
        try {
            $this->aggiungiAnnuncioAllaWishlist($idUtente, $idAnnuncio);

            header('Location: index.php?route=wishlist');
            exit;
        } catch (Exception $e) {
            http_response_code(400);
            $errore = $e->getMessage();
            require __DIR__ . '/../views/errors/400.php';
        }
    }

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
            http_response_code(400);
            $errore = $e->getMessage();
            require __DIR__ . '/../views/errors/400.php';
        }
    }

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
            http_response_code(400);
            $errore = $e->getMessage();
            require __DIR__ . '/../views/errors/400.php';
        }
    }

    public function svuota(int $idUtente): void
    {
        try {
            $this->requirePositiveId($idUtente, 'Utente');
            $this->denyBusinessBuyer($idUtente);

            FPersistentManager::clearPreferitiForUser($idUtente);

            header('Location: index.php?route=wishlist');
            exit;
        } catch (Exception $e) {
            http_response_code(400);
            $errore = $e->getMessage();
            require __DIR__ . '/../views/errors/400.php';
        }
    }

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
