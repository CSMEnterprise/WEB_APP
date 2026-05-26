<?php

namespace App\Controllers;

use App\Entity\EAnnuncio;
use App\Entity\EElementoCarrello;
use App\Foundation\FDataBase;
use App\Foundation\FPersistentManager;
use App\Services\ServiceException;
use Exception;
use PDO;

class CarrelloController extends BaseController
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

            $idCarrello = FPersistentManager::getOrCreateCartIdByUser($idUtente);
            $annunciRimossi = FPersistentManager::unavailableCartItems($idCarrello);

            if (!empty($annunciRimossi)) {
                FPersistentManager::removeUnavailableCartItems($idCarrello);
            }

            $carrello = $this->entitiesToArrays(FPersistentManager::elementiCarrelloAcquistabili($idCarrello));
            $totale = 0.0;

            foreach ($carrello as $item) {
                $isOwner = (int)($item['id_utente'] ?? 0) === $idUtente;

                if (!$isOwner && ($item['stato'] ?? '') === 'attivo') {
                    $totale += (float)($item['prezzo'] ?? 0);
                }
            }

            require __DIR__ . '/../views/carrello/lista.php';
        } catch (Exception $e) {
            http_response_code(400);
            $errore = $e->getMessage();
            require __DIR__ . '/../views/errors/400.php';
        }
    }

    public function aggiungi(int $idUtente, int $idAnnuncio): void
    {
        try {
            $this->aggiungiAnnuncioAlCarrello($idUtente, $idAnnuncio);

            $back = $_SERVER['HTTP_REFERER'] ?? 'index.php?route=annunci';
            header('Location: ' . $back);
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

            $idCarrello = FPersistentManager::getOrCreateCartIdByUser($idUtente);
            FPersistentManager::removeElementoCarrello($idCarrello, $idAnnuncio);

            header('Location: index.php?route=carrello');
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

            $idCarrello = FPersistentManager::getOrCreateCartIdByUser($idUtente);
            FPersistentManager::clearCart($idCarrello);

            header('Location: index.php?route=carrello');
            exit;
        } catch (Exception $e) {
            http_response_code(400);
            $errore = $e->getMessage();
            require __DIR__ . '/../views/errors/400.php';
        }
    }

    private function aggiungiAnnuncioAlCarrello(int $idUtente, int $idAnnuncio): void
    {
        $this->requirePositiveId($idUtente, 'Utente');
        $this->requirePositiveId($idAnnuncio, 'Annuncio');
        $this->denyBusinessBuyer($idUtente);

        $annuncio = FPersistentManager::annuncioById($idAnnuncio);

        if (!$annuncio instanceof EAnnuncio) {
            throw new ServiceException('Annuncio non trovato.');
        }

        if (!$annuncio->isAttivo()) {
            throw new ServiceException("Questo annuncio non e' acquistabile.");
        }

        if ((int)($annuncio->getIdUtente() ?? 0) === $idUtente) {
            throw new ServiceException('Non puoi aggiungere al carrello un tuo annuncio.');
        }

        $idCarrello = FPersistentManager::getOrCreateCartIdByUser($idUtente);

        FPersistentManager::addElementoCarrello(new EElementoCarrello($idCarrello, $idAnnuncio));
        FPersistentManager::removePreferito($idUtente, $idAnnuncio);
    }
}
